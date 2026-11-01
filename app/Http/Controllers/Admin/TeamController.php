<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Edition;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource with filters.
     */
    public function index(Request $request): View
    {
        $query = Team::query()
            ->with(['edition', 'sport', 'department'])
            ->withCount('players')
            ->latest('id');

        // Filter by edition
        if ($request->filled('edition_id')) {
            $query->where('edition_id', $request->input('edition_id'));
        }

        // Filter by sport
        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->input('sport_id'));
        }

        $teams = $query->paginate(15)->withQueryString();

        $editions = Edition::query()->orderByDesc('year')->get();
        $sports = Sport::query()->orderBy('name')->orderBy('category')->get();

        return view('admin.teams.index', compact('teams', 'editions', 'sports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $editions = Edition::query()->orderByDesc('year')->get();
        $sports = Sport::query()->orderBy('name')->orderBy('category')->get();
        $departments = Department::query()->orderBy('name')->get();

        return view('admin.teams.create', compact('editions', 'sports', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'edition_id' => ['required', 'exists:editions,id'],
            'sport_id' => ['required', 'exists:sports,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:pending,approved,rejected'],
        ], [
            'edition_id.required' => 'Please select an edition.',
            'sport_id.required' => 'Please select a sport.',
            'department_id.required' => 'Please select a department.',
        ]);

        $request->validate([
            'department_id' => [
                Rule::unique('teams')
                    ->where(fn ($q) => $q
                        ->where('edition_id', $request->input('edition_id'))
                        ->where('sport_id', $request->input('sport_id'))
                        ->where('department_id', $request->input('department_id'))
                    ),
            ],
        ], [
            'department_id.unique' => 'A team for this department and sport already exists in the selected edition.',
        ]);

        $team = Team::create($validated);

        return redirect()
            ->route('admin.teams.edit', $team)
            ->with('success', "Team '{$team->name}' created successfully. You can now add players below.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team): RedirectResponse
    {
        return redirect()->route('admin.teams.edit', $team);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team): View
    {
        $team->load(['players', 'edition', 'sport', 'department']);

        $editions = Edition::query()->orderByDesc('year')->get();
        $sports = Sport::query()->orderBy('name')->orderBy('category')->get();
        $departments = Department::query()->orderBy('name')->get();

        return view('admin.teams.edit', compact('team', 'editions', 'sports', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team): RedirectResponse
    {
        $validated = $request->validate([
            'edition_id' => ['required', 'exists:editions,id'],
            'sport_id' => ['required', 'exists:sports,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $request->validate([
            'department_id' => [
                Rule::unique('teams')
                    ->where(fn ($q) => $q
                        ->where('edition_id', $request->input('edition_id'))
                        ->where('sport_id', $request->input('sport_id'))
                        ->where('department_id', $request->input('department_id'))
                    )
                    ->ignore($team->id),
            ],
        ], [
            'department_id.unique' => 'A team for this department and sport already exists in the selected edition.',
        ]);

        $team->update($validated);

        return redirect()
            ->route('admin.teams.edit', $team)
            ->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team): RedirectResponse
    {
        $team->delete();

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Team deleted successfully.');
    }

    /**
     * Copy teams from previous edition to the chosen/current edition.
     */
    public function copyFromEdition(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_edition_id' => ['required', 'exists:editions,id'],
            'target_edition_id' => ['required', 'exists:editions,id', 'different:source_edition_id'],
        ], [
            'source_edition_id.required' => 'Please select the source edition to copy teams from.',
            'target_edition_id.required' => 'Please select the target edition to copy teams to.',
            'target_edition_id.different' => 'The target edition must be different from the source edition.',
        ]);

        $sourceEdition = Edition::findOrFail($validated['source_edition_id']);
        $targetEdition = Edition::findOrFail($validated['target_edition_id']);

        $sourceTeams = Team::where('edition_id', $sourceEdition->id)->get();

        if ($sourceTeams->isEmpty()) {
            return redirect()
                ->route('admin.teams.index', ['edition_id' => $targetEdition->id])
                ->with('error', "No teams were found in {$sourceEdition->title} ({$sourceEdition->year}) to copy.");
        }

        $copiedCount = 0;
        $skippedCount = 0;

        foreach ($sourceTeams as $sourceTeam) {
            // Check for duplicate in target edition
            $exists = Team::where('edition_id', $targetEdition->id)
                ->where('sport_id', $sourceTeam->sport_id)
                ->where('department_id', $sourceTeam->department_id)
                ->exists();

            if ($exists) {
                $skippedCount++;
                continue;
            }

            Team::create([
                'edition_id' => $targetEdition->id,
                'sport_id' => $sourceTeam->sport_id,
                'department_id' => $sourceTeam->department_id,
                'name' => $sourceTeam->name,
                'status' => $sourceTeam->status ?? 'approved',
            ]);

            $copiedCount++;
        }

        $message = "Successfully copied {$copiedCount} team(s) from {$sourceEdition->title} to {$targetEdition->title}.";
        if ($skippedCount > 0) {
            $message .= " ({$skippedCount} duplicate team(s) skipped).";
        }

        return redirect()
            ->route('admin.teams.index', ['edition_id' => $targetEdition->id])
            ->with('success', $message);
    }
}

