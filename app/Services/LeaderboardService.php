<?php

namespace App\Services;

use App\Models\Edition;
use App\Models\Fixture;
use App\Models\MatchEvent;
use App\Models\Player;
use App\Models\Sport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LeaderboardService
{
    /**
     * Get aggregated player leaderboards for an edition and optional sport.
     *
     * @param int|Edition $edition
     * @param int|Sport|null $sport
     * @return array<string, mixed>
     */
    public function getLeaderboards(int|Edition $edition, int|Sport|null $sport = null): array
    {
        $editionId = $edition instanceof Edition ? $edition->id : $edition;
        $sportId = $sport instanceof Sport ? $sport->id : $sport;

        $cacheKey = "gala_leaderboard_{$editionId}_" . ($sportId ?? 'all');

        return Cache::remember($cacheKey, 30, function () use ($editionId, $sportId) {
            // 1. MVP / Player of the Match Leaders
            $mvpQuery = Fixture::with(['result.playerOfTheMatch.team.department', 'result.playerOfTheMatch.team.sport'])
                ->where('edition_id', $editionId)
                ->whereHas('result.playerOfTheMatch');

            if ($sportId) {
                $mvpQuery->where('sport_id', $sportId);
            }

            $mvpLeaders = $mvpQuery->get()
                ->map(fn ($f) => $f->result->playerOfTheMatch)
                ->filter()
                ->groupBy('id')
                ->map(function ($group) {
                    $player = $group->first();
                    $player->award_count = $group->count();
                    return $player;
                })
                ->sortByDesc('award_count')
                ->take(10)
                ->values();

            // 2. Football / Futsal Top Goalscorers (Golden Boot)
            $goalEventsQuery = MatchEvent::with(['player.team.department', 'fixture.sport'])
                ->whereHas('fixture', function ($q) use ($editionId, $sportId) {
                    $q->where('edition_id', $editionId);
                    if ($sportId) {
                        $q->where('sport_id', $sportId);
                    }
                })
                ->where('event_type', 'goal')
                ->whereNotNull('player_id');

            $topGoalscorers = $goalEventsQuery->get()
                ->groupBy('player_id')
                ->map(function ($events) {
                    $player = $events->first()->player;
                    if (! $player) {
                        return null;
                    }
                    $player->goals_count = $events->count();
                    $player->latest_sport = $events->first()->fixture?->sport?->name;
                    return $player;
                })
                ->filter()
                ->sortByDesc('goals_count')
                ->take(10)
                ->values();

            // 3. Cricket Top Wicket Takers (Purple Cap)
            $wicketEventsQuery = MatchEvent::with(['player.team.department', 'fixture.sport'])
                ->whereHas('fixture', function ($q) use ($editionId, $sportId) {
                    $q->where('edition_id', $editionId);
                    if ($sportId) {
                        $q->where('sport_id', $sportId);
                    }
                })
                ->where('event_type', 'wicket')
                ->whereNotNull('player_id');

            $topWicketTakers = $wicketEventsQuery->get()
                ->groupBy('player_id')
                ->map(function ($events) {
                    $player = $events->first()->player;
                    if (! $player) {
                        return null;
                    }
                    $player->wickets_count = $events->count();
                    return $player;
                })
                ->filter()
                ->sortByDesc('wickets_count')
                ->take(10)
                ->values();

            // 4. Six Kings / Big Hitters (Sixes)
            $sixEventsQuery = MatchEvent::with(['player.team.department', 'fixture.sport'])
                ->whereHas('fixture', function ($q) use ($editionId, $sportId) {
                    $q->where('edition_id', $editionId);
                    if ($sportId) {
                        $q->where('sport_id', $sportId);
                    }
                })
                ->where('event_type', 'six')
                ->whereNotNull('player_id');

            $topSixHitters = $sixEventsQuery->get()
                ->groupBy('player_id')
                ->map(function ($events) {
                    $player = $events->first()->player;
                    if (! $player) {
                        return null;
                    }
                    $player->sixes_count = $events->count();
                    return $player;
                })
                ->filter()
                ->sortByDesc('sixes_count')
                ->take(10)
                ->values();

            return [
                'mvp_leaders' => $mvpLeaders,
                'top_goalscorers' => $topGoalscorers,
                'top_wicket_takers' => $topWicketTakers,
                'top_six_hitters' => $topSixHitters,
            ];
        });
    }
}

