<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Winner;
use Illuminate\Support\Collection;

class MedalTallyService
{
    /**
     * Points configuration per medal.
     */
    public const POINTS_GOLD = 5;
    public const POINTS_SILVER = 3;
    public const POINTS_BRONZE = 1;

    /**
     * Calculate Department Medal Tally standings for a given edition.
     *
     * @param int|Edition $edition
     * @return Collection<int, array<string, mixed>>
     */
    public function calculateForEdition(int|Edition $edition): Collection
    {
        $editionId = $edition instanceof Edition ? $edition->id : $edition;

        // Fetch all winners for this edition with relations
        $winners = Winner::with([
            'sport',
            'winnerDepartment',
            'runnerUpDepartment',
            'secondRunnerUpDepartment',
        ])
        ->where('edition_id', $editionId)
        ->get();

        // Fetch all registered departments so even 0-medal participating departments can be considered if needed,
        // or prioritize departments with medals
        $tallies = [];

        foreach ($winners as $winner) {
            $sportName = $winner->sport?->name ?? 'Sport';

            // 1. Gold Medal (Winner)
            if ($winner->winnerDepartment) {
                $deptId = $winner->winner_department_id;
                if (! isset($tallies[$deptId])) {
                    $tallies[$deptId] = $this->initRow($winner->winnerDepartment);
                }
                $tallies[$deptId]['gold']++;
                $tallies[$deptId]['total_medals']++;
                $tallies[$deptId]['points'] += self::POINTS_GOLD;
                $tallies[$deptId]['breakdown']['gold'][] = $sportName;
            }

            // 2. Silver Medal (Runner Up)
            if ($winner->runnerUpDepartment) {
                $deptId = $winner->runner_up_department_id;
                if (! isset($tallies[$deptId])) {
                    $tallies[$deptId] = $this->initRow($winner->runnerUpDepartment);
                }
                $tallies[$deptId]['silver']++;
                $tallies[$deptId]['total_medals']++;
                $tallies[$deptId]['points'] += self::POINTS_SILVER;
                $tallies[$deptId]['breakdown']['silver'][] = $sportName;
            }

            // 3. Bronze Medal (Second Runner Up)
            if ($winner->secondRunnerUpDepartment) {
                $deptId = $winner->second_runner_up_department_id;
                if (! isset($tallies[$deptId])) {
                    $tallies[$deptId] = $this->initRow($winner->secondRunnerUpDepartment);
                }
                $tallies[$deptId]['bronze']++;
                $tallies[$deptId]['total_medals']++;
                $tallies[$deptId]['points'] += self::POINTS_BRONZE;
                $tallies[$deptId]['breakdown']['bronze'][] = $sportName;
            }
        }

        // Sort by Gold (desc), Silver (desc), Bronze (desc), Points (desc), Name (asc)
        $sorted = collect($tallies)->sort(function ($a, $b) {
            if ($a['gold'] !== $b['gold']) {
                return $b['gold'] <=> $a['gold'];
            }
            if ($a['silver'] !== $b['silver']) {
                return $b['silver'] <=> $a['silver'];
            }
            if ($a['bronze'] !== $b['bronze']) {
                return $b['bronze'] <=> $a['bronze'];
            }
            if ($a['points'] !== $b['points']) {
                return $b['points'] <=> $a['points'];
            }
            return strcmp($a['department']->name, $b['department']->name);
        })->values();

        // Assign ranks (1, 2, 3...)
        $rank = 1;
        $ranked = $sorted->map(function ($item, $index) use (&$rank, $sorted) {
            if ($index > 0) {
                $prev = $sorted[$index - 1];
                $isTied = $item['gold'] === $prev['gold'] &&
                          $item['silver'] === $prev['silver'] &&
                          $item['bronze'] === $prev['bronze'] &&
                          $item['points'] === $prev['points'];
                if (! $isTied) {
                    $rank = $index + 1;
                }
            }
            $item['rank'] = $rank;
            return $item;
        });

        return $ranked;
    }

    /**
     * Initialize an empty tally row for a department.
     *
     * @param Department $department
     * @return array<string, mixed>
     */
    private function initRow(Department $department): array
    {
        return [
            'rank' => 0,
            'department' => $department,
            'gold' => 0,
            'silver' => 0,
            'bronze' => 0,
            'total_medals' => 0,
            'points' => 0,
            'breakdown' => [
                'gold' => [],
                'silver' => [],
                'bronze' => [],
            ],
        ];
    }
}

