<?php

namespace App\Services;

use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Sport;
use App\Models\Team;
use App\Models\Winner;
use Illuminate\Support\Collection;

class BracketService
{
    /**
     * Standard order of knockout stages.
     */
    protected array $roundOrder = [
        'Round of 16' => 1,
        'Quarter Finals' => 2,
        'Semi Finals' => 3,
        'Final' => 4,
        '3rd Place Playoff' => 5,
    ];

    /**
     * Build tournament bracket & group stages data for a given edition and sport.
     *
     * @param int|Edition $edition
     * @param int|Sport $sport
     * @return array<string, mixed>
     */
    public function getBracket(int|Edition $edition, int|Sport $sport): array
    {
        $editionId = $edition instanceof Edition ? $edition->id : $edition;
        $sportId = $sport instanceof Sport ? $sport->id : $sport;

        $fixtures = Fixture::with([
            'sport',
            'team1.department',
            'team2.department',
            'venue',
            'result.winnerTeam',
        ])
        ->where('edition_id', $editionId)
        ->where('sport_id', $sportId)
        ->orderBy('match_date', 'asc')
        ->orderBy('match_time', 'asc')
        ->get();

        if ($fixtures->isEmpty()) {
            return [
                'has_knockout' => false,
                'has_groups' => false,
                'groups' => collect(),
                'rounds' => collect(),
                'champion' => null,
                'total_matches' => 0,
            ];
        }

        // 1. Separate Group/Pool Matches and Knockout Matches
        $groupFixtures = collect();
        $knockoutFixtures = collect();

        foreach ($fixtures as $fixture) {
            if ($this->isGroupStage($fixture->round)) {
                $groupFixtures->push($fixture);
            } else {
                $knockoutFixtures->push($fixture);
            }
        }

        // 2. Compute Group Stages Standings Tables if present
        $groupStandings = collect();
        if ($groupFixtures->isNotEmpty()) {
            $sportModel = $sport instanceof Sport ? $sport : Sport::find($sportId);
            $isCricket = str_contains(strtolower($sportModel?->name ?? ''), 'cricket');
            $winPoints = $isCricket ? 2 : 3;
            $drawPoints = 1;

            $groupedByPool = $groupFixtures->groupBy(function ($f) {
                return $this->extractGroupName($f->round);
            })->sortKeys();

            foreach ($groupedByPool as $groupName => $poolMatches) {
                $teamsMap = [];

                foreach ($poolMatches as $m) {
                    if ($m->team1 && !isset($teamsMap[$m->team1_id])) {
                        $teamsMap[$m->team1_id] = $this->initGroupRow($m->team1);
                    }
                    if ($m->team2 && !isset($teamsMap[$m->team2_id])) {
                        $teamsMap[$m->team2_id] = $this->initGroupRow($m->team2);
                    }

                    if ($m->result && $m->status === 'completed') {
                        $res = $m->result;
                        $t1 = $m->team1_id;
                        $t2 = $m->team2_id;

                        if (isset($teamsMap[$t1])) {
                            $teamsMap[$t1]['played']++;
                            $teamsMap[$t1]['score_for'] += (int)$res->team1_score;
                            $teamsMap[$t1]['score_against'] += (int)$res->team2_score;
                            $teamsMap[$t1]['score_diff'] = $teamsMap[$t1]['score_for'] - $teamsMap[$t1]['score_against'];

                            if ($res->winner_team_id === $t1) {
                                $teamsMap[$t1]['won']++;
                                $teamsMap[$t1]['points'] += $winPoints;
                            } elseif ($res->winner_team_id === null) {
                                $teamsMap[$t1]['drawn']++;
                                $teamsMap[$t1]['points'] += $drawPoints;
                            } else {
                                $teamsMap[$t1]['lost']++;
                            }
                        }

                        if (isset($teamsMap[$t2])) {
                            $teamsMap[$t2]['played']++;
                            $teamsMap[$t2]['score_for'] += (int)$res->team2_score;
                            $teamsMap[$t2]['score_against'] += (int)$res->team1_score;
                            $teamsMap[$t2]['score_diff'] = $teamsMap[$t2]['score_for'] - $teamsMap[$t2]['score_against'];

                            if ($res->winner_team_id === $t2) {
                                $teamsMap[$t2]['won']++;
                                $teamsMap[$t2]['points'] += $winPoints;
                            } elseif ($res->winner_team_id === null) {
                                $teamsMap[$t2]['drawn']++;
                                $teamsMap[$t2]['points'] += $drawPoints;
                            } else {
                                $teamsMap[$t2]['lost']++;
                            }
                        }
                    }
                }

                // Sort group standings: Points DESC, Score Diff DESC, Won DESC
                $sortedTeams = collect($teamsMap)->sort(function ($a, $b) {
                    if ($a['points'] !== $b['points']) {
                        return $b['points'] <=> $a['points'];
                    }
                    if ($a['score_diff'] !== $b['score_diff']) {
                        return $b['score_diff'] <=> $a['score_diff'];
                    }
                    return $b['won'] <=> $a['won'];
                })->values();

                $groupStandings->push([
                    'name' => $groupName,
                    'teams' => $sortedTeams,
                    'matches_count' => $poolMatches->count(),
                ]);
            }
        }

        // 3. Group Knockout Fixtures into Standard Rounds
        $groupedKnockout = [];

        // If no explicit knockout fixtures but group fixtures exist and total is small, or all matches are knockout
        $fixturesForRounds = $knockoutFixtures->isNotEmpty() ? $knockoutFixtures : $fixtures;

        foreach ($fixturesForRounds as $fixture) {
            $stage = $this->classifyRound($fixture->round);

            if (! isset($groupedKnockout[$stage])) {
                $groupedKnockout[$stage] = collect();
            }

            $groupedKnockout[$stage]->push($fixture);
        }

        // Sort stages in knockout progression order
        $sortedRounds = collect($groupedKnockout)->sortBy(function ($items, $stageName) {
            return $this->roundOrder[$stageName] ?? 99;
        });

        // Determine if this sport has knockout fixtures
        $hasKnockout = $sortedRounds->keys()->contains(function ($stage) {
            return in_array($stage, ['Quarter Finals', 'Semi Finals', 'Final', 'Round of 16']);
        });

        // Determine champion
        $champion = null;

        if (isset($groupedKnockout['Final'])) {
            $finalMatch = $groupedKnockout['Final']->first(function ($f) {
                return $f->status === 'completed' && $f->result && $f->result->winner_team_id;
            });

            if ($finalMatch && $finalMatch->result && $finalMatch->result->winnerTeam) {
                $champion = [
                    'team' => $finalMatch->result->winnerTeam,
                    'department' => $finalMatch->result->winnerTeam->department,
                    'source' => 'final_fixture',
                ];
            }
        }

        if (! $champion) {
            $officialWinner = Winner::with('winnerDepartment')
                ->where('edition_id', $editionId)
                ->where('sport_id', $sportId)
                ->first();

            if ($officialWinner && $officialWinner->winnerDepartment) {
                $champion = [
                    'department' => $officialWinner->winnerDepartment,
                    'source' => 'official_winner',
                ];
            }
        }

        return [
            'has_knockout' => $hasKnockout,
            'has_groups' => $groupStandings->isNotEmpty(),
            'groups' => $groupStandings,
            'rounds' => $sortedRounds,
            'champion' => $champion,
            'total_matches' => $fixtures->count(),
        ];
    }

    /**
     * Check if a round string denotes a group / pool stage.
     */
    protected function isGroupStage(?string $roundName): bool
    {
        if (! $roundName) return false;
        $norm = strtolower($roundName);
        return str_contains($norm, 'group') || str_contains($norm, 'pool') || str_contains($norm, 'league');
    }

    /**
     * Extract human readable group name (e.g. "Group A", "Pool B").
     */
    protected function extractGroupName(?string $roundName): string
    {
        if (! $roundName) return 'Group Stage';

        if (preg_match('/(group\s+[a-z0-9]|pool\s+[a-z0-9])/i', $roundName, $matches)) {
            return ucwords(strtolower($matches[1]));
        }

        return ucwords(trim($roundName));
    }

    /**
     * Initialize group standing row for a team.
     */
    protected function initGroupRow(Team $team): array
    {
        return [
            'team' => $team,
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'score_for' => 0,
            'score_against' => 0,
            'score_diff' => 0,
            'points' => 0,
        ];
    }

    /**
     * Classify free-text round name into standard bracket stages.
     */
    protected function classifyRound(?string $roundName): string
    {
        if (! $roundName) {
            return 'Matches';
        }

        $normalized = strtolower(trim($roundName));

        if (str_contains($normalized, '3rd') || str_contains($normalized, 'bronze') || str_contains($normalized, 'playoff')) {
            return '3rd Place Playoff';
        }

        if (str_contains($normalized, 'semi') || str_contains($normalized, 'sf')) {
            return 'Semi Finals';
        }

        if (str_contains($normalized, 'quarter') || str_contains($normalized, 'qf')) {
            return 'Quarter Finals';
        }

        if (str_contains($normalized, 'round of 16') || str_contains($normalized, 'pre-quarter') || str_contains($normalized, 'r16')) {
            return 'Round of 16';
        }

        if (str_contains($normalized, 'final')) {
            return 'Final';
        }

        return $roundName; // Keep original if custom round
    }
}
