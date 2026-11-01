<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Sport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScoreEntryController extends Controller
{
    /**
     * Display the Score Entry interface.
     */
    public function index(Request $request): View
    {
        $selectedFixture = null;
        if ($request->filled('fixture_id')) {
            $selectedFixture = Fixture::with([
                'edition',
                'sport',
                'team1.department',
                'team2.department',
                'venue',
                'result.winnerTeam',
                'photos.uploader',
            ])->find($request->fixture_id);
        }

        $fixturesQuery = Fixture::with(['edition', 'sport', 'team1.department', 'team2.department', 'venue', 'result'])
            ->orderByRaw("CASE WHEN status = 'live' THEN 1 WHEN status = 'scheduled' THEN 2 ELSE 3 END")
            ->orderBy('match_date', 'asc')
            ->orderBy('match_time', 'asc')
            ->latest('id');

        if ($request->filled('edition_id')) {
            $fixturesQuery->where('edition_id', $request->edition_id);
        }

        if ($request->filled('sport_id')) {
            $fixturesQuery->where('sport_id', $request->sport_id);
        }

        $fixtures = $fixturesQuery->get();
        $editions = Edition::orderBy('year', 'desc')->get();
        $sports = Sport::orderBy('name')->get();

        return view('admin.score-entry.index', compact('fixtures', 'selectedFixture', 'editions', 'sports'));
    }

    /**
     * Store or update the result of a fixture.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fixture_id' => ['required', 'exists:fixtures,id'],
            'team1_score' => ['required', 'integer', 'min:0'],
            'team2_score' => ['required', 'integer', 'min:0'],
            'summary' => ['nullable', 'string', 'max:2000'],
        ]);

        $fixture = Fixture::with(['team1', 'team2'])->findOrFail($validated['fixture_id']);

        // Auto-detect winner from scores (null for draw)
        $winnerTeamId = null;
        if ($validated['team1_score'] > $validated['team2_score']) {
            $winnerTeamId = $fixture->team1_id;
        } elseif ($validated['team2_score'] > $validated['team1_score']) {
            $winnerTeamId = $fixture->team2_id;
        } else {
            $winnerTeamId = null; // Draw
        }

        DB::transaction(function () use ($fixture, $validated, $winnerTeamId) {
            Result::updateOrCreate(
                ['fixture_id' => $fixture->id],
                [
                    'team1_score' => $validated['team1_score'],
                    'team2_score' => $validated['team2_score'],
                    'winner_team_id' => $winnerTeamId,
                    'summary' => $validated['summary'] ?? null,
                ]
            );

            // Automatically set fixture status to completed
            $fixture->update(['status' => 'completed']);
        });

        $winnerName = $winnerTeamId
            ? ($winnerTeamId == $fixture->team1_id ? $fixture->team1->name : $fixture->team2->name)
            : null;

        $msg = $winnerName
            ? "Result saved successfully. Match completed! Winner: {$winnerName}."
            : "Result saved successfully. Match completed! Result: Draw.";

        $redirectRoute = $request->routeIs('admin.score-entry.*')
            ? 'admin.score-entry.index'
            : 'score-entry.index';

        return redirect()->route($redirectRoute, ['fixture_id' => $fixture->id])
            ->with('success', $msg);
    }
}

