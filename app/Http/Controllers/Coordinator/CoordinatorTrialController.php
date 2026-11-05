<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Sport;
use App\Models\StudentTrial;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoordinatorTrialController extends Controller
{
    /**
     * Display trial registrations for this coordinator's department.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $department = $user->department;

        $query = StudentTrial::with(['edition', 'sport'])
            ->where('department_id', $department->id);

        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $trials = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total' => StudentTrial::where('department_id', $department->id)->count(),
            'pending' => StudentTrial::where('department_id', $department->id)->where('status', 'pending')->count(),
            'shortlisted' => StudentTrial::where('department_id', $department->id)->where('status', 'shortlisted')->count(),
            'selected' => StudentTrial::where('department_id', $department->id)->where('status', 'selected')->count(),
        ];

        $sports = Sport::orderBy('name')->get();

        return view('coordinator.trials.index', compact('trials', 'department', 'stats', 'sports'));
    }

    /**
     * Update trial status (shortlist, select, reject).
     */
    public function updateStatus(Request $request, StudentTrial $trial): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($trial->department_id === $user->department_id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,shortlisted,selected,rejected'],
            'coordinator_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $trial->update($validated);

        return back()->with('success', "Trialist {$trial->student_name} marked as " . ucfirst($validated['status']) . '.');
    }

    /**
     * Draft a selected trialist directly into the department's Team squad.
     */
    public function draftToSquad(StudentTrial $trial): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($trial->department_id === $user->department_id, 403);

        // Find existing team or create draft team for this sport and edition
        $team = Team::firstOrCreate(
            [
                'edition_id' => $trial->edition_id,
                'sport_id' => $trial->sport_id,
                'department_id' => $trial->department_id,
            ],
            [
                'name' => ($user->department?->code ?? 'Dept') . ' ' . $trial->sport->name,
                'status' => 'pending',
            ]
        );

        // Check if player already drafted
        $existingPlayer = Player::where('team_id', $team->id)
            ->where('reg_no', $trial->reg_no)
            ->first();

        if (! $existingPlayer) {
            Player::create([
                'team_id' => $team->id,
                'name' => $trial->student_name,
                'reg_no' => $trial->reg_no,
                'contact' => $trial->contact,
            ]);
        }

        $trial->update([
            'status' => 'selected',
            'coordinator_notes' => 'Drafted into team squad.',
        ]);

        return back()->with('success', "Athlete {$trial->student_name} successfully drafted into {$team->name} roster!");
    }
}

