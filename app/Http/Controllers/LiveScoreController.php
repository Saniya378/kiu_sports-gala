<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\Fixture;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LiveScoreController extends Controller
{
    /**
     * Get live scores and status for all matches today (or running edition).
     * Micro-cached for high-traffic scalability under concurrent spectator loads.
     */
    public function index(Request $request): JsonResponse
    {
        $sportId = $request->input('sport_id', 'all');
        $cacheKey = 'live_scores_today_' . $sportId;

        $data = Cache::remember($cacheKey, 8, function () use ($request) {
            $runningEdition = Edition::where('status', 'running')->first();

            if (! $runningEdition) {
                return [
                    'live_count' => 0,
                    'timestamp' => now()->toIso8601String(),
                    'matches' => [],
                ];
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
                    'has_stream' => !empty($fixture->stream_url),
                    'stream_url' => $fixture->stream_url,
                    'url' => route('matches.show', $fixture),
                ];
            });

            return [
                'live_count' => $liveCount,
                'timestamp' => now()->toIso8601String(),
                'matches' => $matches,
            ];
        });

        return response()->json($data);
    }

    /**
     * Get live score details for a specific fixture.
     * Micro-cached for 8 seconds to protect against mass browser polling.
     */
    public function show(Fixture $fixture): JsonResponse
    {
        $cacheKey = "fixture_live_{$fixture->id}";

        $data = Cache::remember($cacheKey, 8, function () use ($fixture) {
            $fixture->load([
                'sport',
                'team1.department',
                'team2.department',
                'result.winnerTeam',
                'result.playerOfTheMatch.team.department',
                'venue',
                'events.team',
                'events.player',
            ]);

            return [
                'id' => $fixture->id,
                'sport' => $fixture->sport?->name,
                'status' => $fixture->status,
                'round' => $fixture->round,
                'venue' => $fixture->venue?->name,
                'match_date' => $fixture->match_date?->format('Y-m-d'),
                'match_time' => $fixture->match_time,
                'stream_url' => $fixture->stream_url,
                'embed_stream_url' => $fixture->embed_stream_url,
                'stream_platform' => $fixture->stream_platform,
                'whatsapp_share_url' => $fixture->whatsapp_share_url,
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
                    'summary' => $fixture->result->summary,
                    'player_of_the_match' => $fixture->result->playerOfTheMatch ? [
                        'id' => $fixture->result->playerOfTheMatch->id,
                        'name' => $fixture->result->playerOfTheMatch->name,
                        'reg_no' => $fixture->result->playerOfTheMatch->reg_no,
                        'team_name' => $fixture->result->playerOfTheMatch->team?->name,
                        'department' => $fixture->result->playerOfTheMatch->team?->department?->name,
                    ] : null,
                ] : null,
                'events' => $fixture->events->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'minute' => $event->minute,
                        'icon' => $event->icon,
                        'event_type' => $event->event_type,
                        'title' => $event->title,
                        'description' => $event->description,
                        'team_name' => $event->team?->name,
                        'player_name' => $event->player?->name,
                    ];
                }),
                'timestamp' => now()->toIso8601String(),
            ];
        });

        return response()->json($data);
    }
}
