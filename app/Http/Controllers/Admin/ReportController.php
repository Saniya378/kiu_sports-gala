<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Sport;
use App\Models\Team;
use App\Models\Winner;
use App\Services\MedalTallyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the reports hub overview.
     */
    public function index(): View
    {
        $editionsCount = Edition::count();
        $fixturesCount = Fixture::count();
        $teamsCount = Team::count();
        $winnersCount = Winner::count();

        return view('admin.reports.index', compact(
            'editionsCount',
            'fixturesCount',
            'teamsCount',
            'winnersCount'
        ));
    }

    /**
     * Print-friendly report: Department Medal Tally (General Trophy Standings).
     */
    public function medals(Request $request, MedalTallyService $medalTallyService): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $selectedEditionId = $request->input('edition_id') ?? $editions->first()?->id;
        $selectedEdition = $selectedEditionId ? Edition::find($selectedEditionId) : null;

        $medalTally = collect();
        if ($selectedEdition) {
            $medalTally = $medalTallyService->calculateForEdition($selectedEdition);
        }

        return view('admin.reports.medals', compact(
            'editions',
            'selectedEdition',
            'medalTally'
        ));
    }

    /**
     * Print-friendly report: Winners list by edition.
     */
    public function winners(Request $request): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $selectedEditionId = $request->input('edition_id') ?? $editions->first()?->id;
        $selectedEdition = $selectedEditionId ? Edition::find($selectedEditionId) : null;

        $winners = collect();
        if ($selectedEdition) {
            $winners = Winner::with(['sport', 'winnerDepartment', 'runnerUpDepartment'])
                ->where('edition_id', $selectedEdition->id)
                ->get()
                ->sortBy(fn($w) => $w->sport->name ?? '');
        }

        return view('admin.reports.winners', compact(
            'editions',
            'selectedEdition',
            'winners'
        ));
    }

    /**
     * Print-friendly report: Fixtures list by date.
     */
    public function fixtures(Request $request): View
    {
        $selectedDate = $request->input('date') ?? Carbon::today()->toDateString();
        $editions = Edition::orderBy('year', 'desc')->get();
        $selectedEditionId = $request->input('edition_id');

        $query = Fixture::with([
            'sport',
            'team1.department',
            'team2.department',
            'venue',
            'edition',
            'result',
        ])
        ->whereDate('match_date', $selectedDate)
        ->orderBy('match_time', 'asc');

        if ($selectedEditionId) {
            $query->where('edition_id', $selectedEditionId);
        }

        $fixtures = $query->get();

        return view('admin.reports.fixtures', compact(
            'editions',
            'selectedDate',
            'selectedEditionId',
            'fixtures'
        ));
    }

    /**
     * Print-friendly report: Teams list by sport.
     */
    public function teams(Request $request): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $selectedEditionId = $request->input('edition_id') ?? $editions->first()?->id;
        $selectedEdition = $selectedEditionId ? Edition::find($selectedEditionId) : null;

        $sports = Sport::orderBy('name')->get();
        $selectedSportId = $request->input('sport_id') ?? $sports->first()?->id;
        $selectedSport = $selectedSportId ? Sport::find($selectedSportId) : null;

        $teams = collect();
        if ($selectedEdition && $selectedSport) {
            $teams = Team::with(['department', 'players'])
                ->where('edition_id', $selectedEdition->id)
                ->where('sport_id', $selectedSport->id)
                ->orderBy('name')
                ->get();
        }

        return view('admin.reports.teams', compact(
            'editions',
            'selectedEdition',
            'sports',
            'selectedSport',
            'teams'
        ));
    }
}

