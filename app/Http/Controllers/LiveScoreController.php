<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\Fixture;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveScoreController extends Controller
{
    /**
     * Get live scores and status for all matches today (or running edition).
     */
    public function index(Request $request): JsonResponse
    {
        $runningEdition = Edition::where('status', 'running')->first();

        if (! $runningEdition) {
            return response()->json([
                'live_count' => 0,
                'timestamp' => now()->toIso8601String(),
                'matches' => [],
            ]);
        }

        $query = Fixture::with(['sport', 'team1.department', 'team2.department', 'result.winnerTeam', 'result.playerOfTheMatch.team'])
            ->where('edition_id', $runningEdition->id)
            ->whereDate('match_date', Carbon::today())
            ->orderBy('match_time', 'asc');

        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        $fixtures = $query->get();

        $liveCount = $fixtures->where('status', 'live')->count();

        $matches = $fixtures->map(function (Fixture $fixture) {
            return [
                'id' => $fixture->id,
                'sport' => $fixture->sport?->name,
                'status' => $fixture->status,
                'round' => $fixture->round,
                'match_time' => $fixture->match_time,
                'team1_id' => $fixture->team1_id,
                'team1_name' => $fixture->team1?->name,
                'team1_department' => $fixture->team1?->department?->name,
                'team1_score' => $fixture->result?->team1_score ?? null,
                'team2_id' => $fixture->team2_id,
                'team2_name' => $fixture->team2?->name,
                'team2_department' => $fixture->team2?->department?->name,
                'team2_score' => $fixture->result?->team2_score ?? null,
                'winner_team_id' => $fixture->result?->winner_team_id,
                'winner_name' => $fixture->result?->winnerTeam?->name,
                'player_of_the_match' => $fixture->result?->playerOfTheMatch ? [
                    'id' => $fixture->result->playerOfTheMatch->id,
                    'name' => $fixture->result->playerOfTheMatch->name,
                    'reg_no' => $fixture->result->playerOfTheMatch->reg_no,
                    'team_name' => $fixture->result->playerOfTheMatch->team?->name,
                ] : null,
                'summary' => $fixture->result?->summary,
                'url' => route('matches.show', $fixture),
            ];
        });

        return response()->json([
            'live_count' => $liveCount,
            'timestamp' => now()->toIso8601String(),
            'matches' => $matches,
        ]);
    }

    /**
     * Get live score details for a specific fixture.
     */
    public function show(Fixture $fixture): JsonResponse
    {
        $fixture->load([
            'sport',
            'team1.department',
            'team2.department',
            'result.winnerTeam',
            'result.playerOfTheMatch.team.department',
            'venue'
        ]);

        return response()->json([
            'id' => $fixture->id,
            'sport' => $fixture->sport?->name,
            'status' => $fixture->status,
            'round' => $fixture->round,
            'venue' => $fixture->venue?->name,
            'match_date' => $fixture->match_date?->format('Y-m-d'),
            'match_time' => $fixture->match_time,
            'team1' => [
                'id' => $fixture->team1_id,
                'name' => $fixture->team1?->name,
                'department' => $fixture->team1?->department?->name,
                'score' => $fixture->result?->team1_score ?? null,
                'is_winner' => $fixture->result && $fixture->result->winner_team_id === $fixture->team1_id,
            ],
            'team2' => [
                'id' => $fixture->team2_id,
                'name' => $fixture->team2?->name,
                'department' => $fixture->team2?->department?->name,
                'score' => $fixture->result?->team2_score ?? null,
                'is_winner' => $fixture->result && $fixture->result->winner_team_id === $fixture->team2_id,
            ],
            'result' => $fixture->result ? [
                'team1_score' => $fixture->result->team1_score,
                'team2_score' => $fixture->result->team2_score,
                'winner_team_id' => $fixture->result->winner_team_id,
                'winner_name' => $fixture->result->winnerTeam?->name,
                'player_of_the_match' => $fixture->result->playerOfTheMatch ? [
                    'id' => $fixture->result->playerOfTheMatch->id,
                    'name' => $fixture->result->playerOfTheMatch->name,
                    'reg_no' => $fixture->result->playerOfTheMatch->reg_no,
                    'team' => $fixture->result->playerOfTheMatch->team?->name,
                    'department' => $fixture->result->playerOfTheMatch->team?->department?->name,
                ] : null,
                'summary' => $fixture->result->summary,
            ] : null,
            'updated_at' => $fixture->result?->updated_at?->toIso8601String() ?? $fixture->updated_at->toIso8601String(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

