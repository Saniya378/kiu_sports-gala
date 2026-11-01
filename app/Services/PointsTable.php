<?php

namespace App\Services;

use App\Models\Edition;
use App\Models\Sport;
use Illuminate\Support\Collection;

class PointsTable extends PointsTableService
{
    /**
     * Static helper to calculate standings for a given edition and sport.
     *
     * @param int|Edition $edition
     * @param int|Sport $sport
     * @return Collection<int, array<string, mixed>>
     */
    public static function for(int|Edition $edition, int|Sport $sport): Collection
    {
        return (new static())->calculate($edition, $sport);
    }
}

