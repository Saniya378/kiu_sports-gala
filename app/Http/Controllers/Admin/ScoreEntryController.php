<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Sport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
                'team1.players',
                'team2.department',
                'team2.players',
                'venue',
                'result.winnerTeam',
                'result.playerOfTheMatch',
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
     * Display Mobile Quick Scorer Interface / Rapid Pad.
     */
    public function quickPad(Fixture $fixture): View
    {
        $fixture->load([
            'edition',
            'sport',
            'venue',
            'team1.department',
            'team1.players',
            'team2.department',
            'team2.players',
            'result.winnerTeam',
            'result.playerOfTheMatch',
            'events.team',
            'events.player',
        ]);

        return view('admin.score-entry.pad', compact('fixture'));
    }

    /**
     * Store or update the result of a fixture.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'fixture_id' => ['required', 'exists:fixtures,id'],
            'team1_score' => ['required', 'integer', 'min:0'],
            'team2_score' => ['required', 'integer', 'min:0'],
            'player_of_the_match_id' => ['nullable', 'exists:players,id'],
            'status' => ['nullable', 'in:scheduled,live,completed,cancelled'],
            'stream_url' => ['nullable', 'url', 'max:500'],
            'summary' => ['nullable', 'string', 'max:2000'],
        ]);

        $fixture = Fixture::with(['team1', 'team2'])->findOrFail($validated['fixture_id']);
        $status = $validated['status'] ?? 'completed';

        // Auto-detect winner from scores when completed
        $winnerTeamId = null;
        if ($status === 'completed') {
            if ($validated['team1_score'] > $validated['team2_score']) {
                $winnerTeamId = $fixture->team1_id;
            } elseif ($validated['team2_score'] > $validated['team1_score']) {
                $winnerTeamId = $fixture->team2_id;
            } else {
                $winnerTeamId = null; // Draw
            }
        }

        DB::transaction(function () use ($fixture, $validated, $winnerTeamId, $status, $request) {
            Result::updateOrCreate(
                ['fixture_id' => $fixture->id],
                [
                    'team1_score' => $validated['team1_score'],
                    'team2_score' => $validated['team2_score'],
                    'winner_team_id' => $winnerTeamId,
                    'player_of_the_match_id' => $validated['player_of_the_match_id'] ?? null,
                    'summary' => $validated['summary'] ?? null,
                ]
            );

            // Update fixture status and optional live stream URL
            $fixtureData = ['status' => $status];
            if ($request->has('stream_url')) {
                $fixtureData['stream_url'] = $validated['stream_url'];
            }
            $fixture->update($fixtureData);
        });

        // Invalidate live scores and standings micro-caches immediately
        Cache::forget('live_scores_today_all');
        if ($fixture->sport_id) {
            Cache::forget("live_scores_today_{$fixture->sport_id}");
        }
        Cache::forget("fixture_live_{$fixture->id}");
        Cache::forget("medal_tally_{$fixture->edition_id}");

        $winnerName = $winnerTeamId
            ? ($winnerTeamId == $fixture->team1_id ? $fixture->team1->name : $fixture->team2->name)
            : null;

        if ($status === 'live') {
            $msg = "Live score updated successfully. Match status is LIVE.";
        } elseif ($winnerName) {
            $msg = "Result saved successfully. Match completed! Winner: {$winnerName}.";
        } else {
            $msg = "Result saved successfully. Match completed! Result: Draw.";
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'fixture' => [
                    'id' => $fixture->id,
                    'status' => $status,
                    'team1_score' => (int) $validated['team1_score'],
                    'team2_score' => (int) $validated['team2_score'],
                    'winner_team_id' => $winnerTeamId,
                ],
            ]);
        }

        $redirectRoute = $request->routeIs('admin.score-entry.*')
            ? 'admin.score-entry.index'
            : 'score-entry.index';

        return redirect()->route($redirectRoute, ['fixture_id' => $fixture->id])
            ->with('success', $msg);
    }
}

