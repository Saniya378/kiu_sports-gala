<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\Fixture;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LiveScoreController extends Controller
{
    /**
     * Get structured live scores data for today's matches.
     * Micro-cached for high-traffic scalability.
     */
    public function getTodayMatchesData(?int $sportId = null): array
    {
        $cacheKey = 'live_scores_today_' . ($sportId ?? 'all');

        return Cache::remember($cacheKey, 8, function () use ($sportId) {
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

            if ($sportId) {
                $query->where('sport_id', $sportId);
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
                    'team1_department_id' => $fixture->team1?->department_id,
                    'team1_department' => $fixture->team1?->department?->name,
                    'team1_score' => $fixture->result?->team1_score ?? null,
                    'team2_id' => $fixture->team2_id,
                    'team2_name' => $fixture->team2?->name,
                    'team2_department_id' => $fixture->team2?->department_id,
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
    }

    /**
     * Get structured live score data for a single fixture.
     */
    public function getFixtureData(Fixture $fixture): array
    {
        $cacheKey = "fixture_live_{$fixture->id}";

        return Cache::remember($cacheKey, 8, function () use ($fixture) {
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
    }

    /**
     * Get live scores and status for all matches today (JSON).
     */
    public function index(Request $request): JsonResponse
    {
        $sportId = $request->filled('sport_id') ? (int) $request->sport_id : null;
        $data = $this->getTodayMatchesData($sportId);

        return response()->json($data);
    }

    /**
     * Get live score details for a specific fixture (JSON).
     */
    public function show(Fixture $fixture): JsonResponse
    {
        $data = $this->getFixtureData($fixture);

        return response()->json($data);
    }

    /**
     * Server-Sent Events (SSE) real-time stream for today's matches.
     */
    public function streamAll(Request $request): StreamedResponse
    {
        $sportId = $request->filled('sport_id') ? (int) $request->sport_id : null;
        $data = $this->getTodayMatchesData($sportId);

        return response()->stream(function () use ($data) {
            echo "retry: 3000\n";
            echo "event: today-live-scores\n";
            echo "data: " . json_encode($data) . "\n\n";
            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Server-Sent Events (SSE) real-time stream for a specific match fixture.
     */
    public function streamMatch(Fixture $fixture): StreamedResponse
    {
        $data = $this->getFixtureData($fixture);

        return response()->stream(function () use ($data) {
            echo "retry: 3000\n";
            echo "event: match-live-score\n";
            echo "data: " . json_encode($data) . "\n\n";
            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
