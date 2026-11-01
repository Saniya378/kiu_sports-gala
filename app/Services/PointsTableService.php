<?php

namespace App\Services;

use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Support\Collection;

class PointsTableService
{
    /**
     * Calculate points table standings for a given edition and sport.
     *
     * @param int|Edition $edition
     * @param int|Sport $sport
     * @return Collection<int, array<string, mixed>>
     */
    public function calculate(int|Edition $edition, int|Sport $sport): Collection
    {
        $editionId = $edition instanceof Edition ? $edition->id : $edition;
        $sportId = $sport instanceof Sport ? $sport->id : $sport;

        // Fetch all teams registered for this edition and sport
        $teams = Team::with('department')
            ->where('edition_id', $editionId)
            ->where('sport_id', $sportId)
            ->get();

        // Fetch completed fixtures with results for this edition and sport
        $fixtures = Fixture::with(['result', 'team1', 'team2'])
            ->where('edition_id', $editionId)
            ->where('sport_id', $sportId)
            ->whereHas('result')
            ->get();

        // Initialize standings structure for each team
        $standings = [];
        foreach ($teams as $team) {
            $standings[$team->id] = $this->emptyRow($team);
        }

        foreach ($fixtures as $fixture) {
            $result = $fixture->result;
            if (! $result) {
                continue;
            }

            $t1Id = $fixture->team1_id;
            $t2Id = $fixture->team2_id;

            // In case a team is part of a fixture but wasn't in initial teams query
            if (! isset($standings[$t1Id]) && $fixture->team1) {
                $standings[$t1Id] = $this->emptyRow($fixture->team1);
            }
            if (! isset($standings[$t2Id]) && $fixture->team2) {
                $standings[$t2Id] = $this->emptyRow($fixture->team2);
            }

            // Team 1 match record
            if (isset($standings[$t1Id])) {
                $standings[$t1Id]['played']++;
                $standings[$t1Id]['score_for'] += $result->team1_score;
                $standings[$t1Id]['score_against'] += $result->team2_score;
                $standings[$t1Id]['score_diff'] = $standings[$t1Id]['score_for'] - $standings[$t1Id]['score_against'];

                if ($result->winner_team_id === $t1Id) {
                    $standings[$t1Id]['won']++;
                    $standings[$t1Id]['points'] += 3;
                } elseif ($result->winner_team_id === null) {
                    $standings[$t1Id]['drawn']++;
                    $standings[$t1Id]['points'] += 1;
                } else {
                    $standings[$t1Id]['lost']++;
                }
            }

            // Team 2 match record
            if (isset($standings[$t2Id])) {
                $standings[$t2Id]['played']++;
                $standings[$t2Id]['score_for'] += $result->team2_score;
                $standings[$t2Id]['score_against'] += $result->team1_score;
                $standings[$t2Id]['score_diff'] = $standings[$t2Id]['score_for'] - $standings[$t2Id]['score_against'];

                if ($result->winner_team_id === $t2Id) {
                    $standings[$t2Id]['won']++;
                    $standings[$t2Id]['points'] += 3;
                } elseif ($result->winner_team_id === null) {
                    $standings[$t2Id]['drawn']++;
                    $standings[$t2Id]['points'] += 1;
                } else {
                    $standings[$t2Id]['lost']++;
                }
            }
        }

        // Return sorted by points desc, then score_diff desc, then team name asc
        return collect($standings)
            ->sort(function ($a, $b) {
                if ($a['points'] !== $b['points']) {
                    return $b['points'] <=> $a['points'];
                }

                if ($a['score_diff'] !== $b['score_diff']) {
                    return $b['score_diff'] <=> $a['score_diff'];
                }

                if ($a['score_for'] !== $b['score_for']) {
                    return $b['score_for'] <=> $a['score_for'];
                }

                return strcmp($a['team']->name, $b['team']->name);
            })
            ->values();
    }

    /**
     * Create an empty standings row for a team.
     *
     * @return array<string, mixed>
     */
    protected function emptyRow(Team $team): array
    {
        return [
            'team' => $team,
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'points' => 0,
            'score_for' => 0,
            'score_against' => 0,
            'score_diff' => 0,
        ];
    }
}

