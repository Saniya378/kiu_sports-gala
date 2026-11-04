<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Player;
use App\Models\Winner;
use App\Services\MedalTallyService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CeremonyController extends Controller
{
    /**
     * Display the full-screen projector / big-screen presentation display.
     */
    public function index(Request $request, MedalTallyService $medalTallyService): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();

        $selectedEditionId = $request->input('edition_id')
            ?? $editions->firstWhere('status', 'running')?->id
            ?? $editions->firstWhere('status', 'completed')?->id
            ?? $editions->first()?->id;

        $edition = $selectedEditionId ? Edition::find($selectedEditionId) : null;

        $medalTally = collect();
        $champion = null;
        $runnerUp = null;
        $thirdPlace = null;
        $winners = collect();
        $totalMatches = 0;
        $topPerformers = collect();

        if ($edition) {
            $tally = $medalTallyService->calculateForEdition($edition);
            $medalTally = $tally;

            $champion = $tally->first() ?? null;
            $runnerUp = $tally->slice(1, 1)->first() ?? null;
            $thirdPlace = $tally->slice(2, 1)->first() ?? null;

            $winners = Winner::with([
                'sport',
                'winnerDepartment',
                'runnerUpDepartment',
                'secondRunnerUpDepartment',
            ])
            ->where('edition_id', $edition->id)
            ->get()
            ->sortBy(fn ($w) => $w->sport->name ?? '');

            $totalMatches = Fixture::where('edition_id', $edition->id)
                ->where('status', 'completed')
                ->count();

            // Fetch top Player of the Match awardees in this edition
            $topPerformers = Fixture::with(['result.playerOfTheMatch.team.department'])
                ->where('edition_id', $edition->id)
                ->whereHas('result.playerOfTheMatch')
                ->get()
                ->map(fn ($f) => $f->result->playerOfTheMatch)
                ->filter()
                ->groupBy('id')
                ->map(function ($group) {
                    $player = $group->first();
                    $player->potm_count = $group->count();
                    return $player;
                })
                ->sortByDesc('potm_count')
                ->take(6)
                ->values();
        }

        return view('public.ceremony', compact(
            'editions',
            'edition',
            'medalTally',
            'champion',
            'runnerUp',
            'thirdPlace',
            'winners',
            'totalMatches',
            'topPerformers'
        ));
    }
}

