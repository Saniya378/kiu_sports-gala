<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Sport;
use App\Services\PointsTableService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PointsTableController extends Controller
{
    /**
     * Display the points table for testing and verification.
     */
    public function index(Request $request, PointsTableService $pointsTableService): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $sports = Sport::orderBy('name')->get();

        $selectedEditionId = $request->input('edition_id')
            ?? Edition::where('status', 'running')->first()?->id
            ?? $editions->first()?->id;

        $selectedSportId = $request->input('sport_id')
            ?? $sports->first()?->id;

        $standings = collect();
        $selectedEdition = null;
        $selectedSport = null;

        if ($selectedEditionId && $selectedSportId) {
            $selectedEdition = Edition::find($selectedEditionId);
            $selectedSport = Sport::find($selectedSportId);

            if ($selectedEdition && $selectedSport) {
                $standings = $pointsTableService->calculate($selectedEdition, $selectedSport);
            }
        }

        return view('admin.points-table.index', compact(
            'editions',
            'sports',
            'selectedEditionId',
            'selectedSportId',
            'selectedEdition',
            'selectedSport',
            'standings'
        ));
    }
}

