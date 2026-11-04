<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Sport;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FixtureController extends Controller
{
    /**
     * Display a listing of fixtures with filters.
     */
    public function index(Request $request): View
    {
        $query = Fixture::with(['edition', 'sport', 'team1.department', 'team2.department', 'venue']);

        if ($request->filled('edition_id')) {
            $query->where('edition_id', $request->edition_id);
        }

        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('match_date', $request->date);
        }

        $fixtures = $query->orderBy('match_date', 'asc')
            ->orderBy('match_time', 'asc')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $editions = Edition::orderBy('year', 'desc')->get();
        $sports = Sport::orderBy('name')->get();

        return view('admin.fixtures.index', compact('fixtures', 'editions', 'sports'));
    }

    /**
     * Show the form for creating a new fixture.
     */
    public function create(): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $sports = Sport::orderBy('name')->get();
        $venues = Venue::orderBy('name')->get();
        $teams = Team::with(['edition', 'sport', 'department'])->orderBy('name')->get();

        return view('admin.fixtures.create', compact('editions', 'sports', 'venues', 'teams'));
    }

    /**
     * Store a newly created fixture in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateFixture($request);

        Fixture::create($validated);

        return redirect()->route('admin.fixtures.index')
            ->with('success', 'Fixture scheduled successfully.');
    }

    /**
     * Show the form for editing the specified fixture.
     */
    public function edit(Fixture $fixture): View
    {
        $fixture->load(['photos.uploader', 'team1', 'team2']);

        $editions = Edition::orderBy('year', 'desc')->get();
        $sports = Sport::orderBy('name')->get();
        $venues = Venue::orderBy('name')->get();
        $teams = Team::with(['edition', 'sport', 'department'])->orderBy('name')->get();

        return view('admin.fixtures.edit', compact('fixture', 'editions', 'sports', 'venues', 'teams'));
    }

    /**
     * Update the specified fixture in storage.
     */
    public function update(Request $request, Fixture $fixture): RedirectResponse
    {
        $validated = $this->validateFixture($request);

        $fixture->update($validated);

        return redirect()->route('admin.fixtures.index')
            ->with('success', 'Fixture updated successfully.');
    }

    /**
     * Remove the specified fixture from storage.
     */
    public function destroy(Fixture $fixture): RedirectResponse
    {
        $fixture->delete();

        return redirect()->route('admin.fixtures.index')
            ->with('success', 'Fixture deleted successfully.');
    }

    /**
     * Validate the fixture request input.
     *
     * @return array<string, mixed>
     */
    protected function validateFixture(Request $request): array
    {
        return $request->validate([
            'edition_id' => ['required', 'exists:editions,id'],
            'sport_id' => ['required', 'exists:sports,id'],
            'team1_id' => [
                'required',
                'different:team2_id',
                'exists:teams,id',
                function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                    $team = Team::find($value);
                    if ($team && ($team->edition_id != $request->edition_id || $team->sport_id != $request->sport_id)) {
                        $fail('Team 1 must belong to the selected edition and sport.');
                    }
                },
            ],
            'team2_id' => [
                'required',
                'different:team1_id',
                'exists:teams,id',
                function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                    $team = Team::find($value);
                    if ($team && ($team->edition_id != $request->edition_id || $team->sport_id != $request->sport_id)) {
                        $fail('Team 2 must belong to the selected edition and sport.');
                    }
                },
            ],
            'venue_id' => ['nullable', 'exists:venues,id'],
            'match_date' => ['nullable', 'date'],
            'match_time' => ['nullable', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'],
            'round' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:scheduled,live,completed,cancelled'],
            'stream_url' => ['nullable', 'url', 'max:500'],
        ], [
            'team1_id.different' => 'Team 1 and Team 2 must be different teams.',
            'team2_id.different' => 'Team 1 and Team 2 must be different teams.',
            'match_time.regex' => 'The match time format must be HH:MM or HH:MM:SS.',
        ]);
    }
}

