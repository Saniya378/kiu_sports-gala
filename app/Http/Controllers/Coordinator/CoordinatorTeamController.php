<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CoordinatorTeamController extends Controller
{
    /**
     * Display teams submitted by this coordinator's department.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $department = $user->department;

        $query = Team::with(['edition', 'sport', 'players'])
            ->withCount('players')
            ->where('department_id', $department->id);

        if ($request->filled('edition_id')) {
            $query->where('edition_id', $request->edition_id);
        }

        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $teams = $query->latest('id')->paginate(12)->withQueryString();

        $stats = [
            'total' => Team::where('department_id', $department->id)->count(),
            'approved' => Team::where('department_id', $department->id)->where('status', 'approved')->count(),
            'pending' => Team::where('department_id', $department->id)->where('status', 'pending')->count(),
            'rejected' => Team::where('department_id', $department->id)->where('status', 'rejected')->count(),
        ];

        $editions = Edition::orderBy('year', 'desc')->get();
        $sports = Sport::orderBy('name')->get();

        return view('coordinator.teams.index', compact('teams', 'department', 'stats', 'editions', 'sports'));
    }

    /**
     * Show form to register a new department squad.
     */
    public function create(): View
    {
        $user = auth()->user();
        $department = $user->department;

        $editions = Edition::whereIn('status', ['running', 'upcoming'])->orderBy('year', 'desc')->get();
        if ($editions->isEmpty()) {
            $editions = Edition::orderBy('year', 'desc')->take(2)->get();
        }

        $sports = Sport::orderBy('name')->orderBy('category')->get();

        return view('coordinator.teams.create', compact('department', 'editions', 'sports'));
    }

    /**
     * Store a newly submitted department squad (status: pending).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $deptId = $user->department_id;

        $validated = $request->validate([
            'edition_id' => ['required', 'exists:editions,id'],
            'sport_id' => ['required', 'exists:sports,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        // Enforce unique team per department, edition, and sport
        $request->validate([
            'sport_id' => [
                Rule::unique('teams')->where(fn ($q) => $q
                    ->where('edition_id', $validated['edition_id'])
                    ->where('sport_id', $validated['sport_id'])
                    ->where('department_id', $deptId)
                ),
            ],
        ], [
            'sport_id.unique' => 'Your department already has a registered team for this sport in the selected edition.',
        ]);

        $team = Team::create([
            'edition_id' => $validated['edition_id'],
            'sport_id' => $validated['sport_id'],
            'department_id' => $deptId,
            'name' => $validated['name'],
            'status' => 'pending', // Submissions require Admin approval
        ]);

        return redirect()->route('coordinator.teams.show', $team)
            ->with('success', "Team '{$team->name}' registered successfully! Now add your athlete squad below. It will be reviewed by the sports directorate.");
    }

    /**
     * Display a department squad, its players, and accreditation status.
     */
    public function show(Team $team): View
    {
        $this->authorizeTeam($team);

        $team->load([
            'edition',
            'sport',
            'department',
            'players',
        ]);

        return view('coordinator.teams.show', compact('team'));
    }

    /**
     * Add a player to the department team.
     */
    public function storePlayer(Request $request, Team $team): RedirectResponse
    {
        $this->authorizeTeam($team);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'reg_no' => ['nullable', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('players', 'public');
        }

        $player = $team->players()->create($validated);

        return redirect()->route('coordinator.teams.show', $team)
            ->with('success', "Athlete '{$player->name}' added to squad successfully.");
    }

    /**
     * Bulk import athletes via CSV file.
     */
    public function importCsv(Request $request, Team $team): RedirectResponse
    {
        $this->authorizeTeam($team);

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('csv_file');
        $content = file_get_contents($file->getRealPath());
        $lines = preg_split('/\r\n|\r|\n/', trim($content));

        if (empty($lines)) {
            return back()->withErrors(['csv_file' => 'Uploaded CSV file is empty.']);
        }

        $imported = 0;
        foreach ($lines as $index => $line) {
            $line = trim($line);
            if ($line === '') continue;

            $row = str_getcsv($line);
            $name = trim($row[0] ?? '');
            $regNo = trim($row[1] ?? '');
            $contact = trim($row[2] ?? '');

            if ($index === 0 && (strtolower($name) === 'name' || strtolower($name) === 'player name')) {
                continue;
            }

            if (empty($name)) continue;

            $exists = $team->players()
                ->where(function ($q) use ($name, $regNo) {
                    $q->where('name', $name);
                    if (!empty($regNo)) {
                        $q->orWhere('reg_no', $regNo);
                    }
                })->exists();

            if (! $exists) {
                $team->players()->create([
                    'name' => mb_substr($name, 0, 255),
                    'reg_no' => !empty($regNo) ? mb_substr($regNo, 0, 100) : null,
                    'contact' => !empty($contact) ? mb_substr($contact, 0, 100) : null,
                ]);
                $imported++;
            }
        }

        return redirect()->route('coordinator.teams.show', $team)
            ->with('success', "Successfully imported {$imported} athlete(s) into squad.");
    }

    /**
     * Delete a player from the squad.
     */
    public function destroyPlayer(Team $team, Player $player): RedirectResponse
    {
        $this->authorizeTeam($team);

        if ($player->team_id !== $team->id) {
            abort(403);
        }

        if ($player->photo && Storage::disk('public')->exists($player->photo)) {
            Storage::disk('public')->delete($player->photo);
        }

        $playerName = $player->name;
        $player->delete();

        return redirect()->route('coordinator.teams.show', $team)
            ->with('success', "Athlete '{$playerName}' removed from squad.");
    }

    /**
     * Resubmit a rejected team for approval.
     */
    public function resubmit(Team $team): RedirectResponse
    {
        $this->authorizeTeam($team);

        $team->update([
            'status' => 'pending',
            'rejection_reason' => null,
        ]);

        return redirect()->route('coordinator.teams.show', $team)
            ->with('success', "Squad re-submitted for Admin approval!");
    }

    /**
     * Ensure the logged in coordinator can only access their own department's team.
     */
    protected function authorizeTeam(Team $team): void
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return;
        }

        if ($team->department_id !== $user->department_id) {
            abort(403, 'Unauthorized: You can only view and manage squads belonging to your own department.');
        }
    }
}

