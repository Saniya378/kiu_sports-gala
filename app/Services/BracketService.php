<?php

namespace App\Services;

use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Sport;
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
     * Build the tournament bracket data for a given edition and sport.
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
                'rounds' => collect(),
                'champion' => null,
                'total_matches' => 0,
            ];
        }

        // Group fixtures into standard rounds
        $grouped = [];

        foreach ($fixtures as $fixture) {
            $stage = $this->classifyRound($fixture->round);

            if (! isset($grouped[$stage])) {
                $grouped[$stage] = collect();
            }

            $grouped[$stage]->push($fixture);
        }

        // Sort the stages in knockout progression order
        $sortedRounds = collect($grouped)->sortBy(function ($items, $stageName) {
            return $this->roundOrder[$stageName] ?? 99;
        });

        // Determine if this sport has knockout fixtures
        $hasKnockout = $sortedRounds->keys()->contains(function ($stage) {
            return in_array($stage, ['Quarter Finals', 'Semi Finals', 'Final', 'Round of 16']);
        });

        // Determine champion: from completed Final fixture or official Winner record
        $champion = null;

        if (isset($grouped['Final'])) {
            $finalMatch = $grouped['Final']->first(function ($f) {
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
            'rounds' => $sortedRounds,
            'champion' => $champion,
            'total_matches' => $fixtures->count(),
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

