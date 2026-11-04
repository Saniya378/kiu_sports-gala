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
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    /**
     * Export Department Medal Tally as CSV for Excel / Directorate records.
     */
    public function exportMedals(Request $request, MedalTallyService $medalTallyService): StreamedResponse
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $selectedEditionId = $request->input('edition_id') ?? $editions->first()?->id;
        $selectedEdition = $selectedEditionId ? Edition::find($selectedEditionId) : null;

        $medalTally = $selectedEdition ? $medalTallyService->calculateForEdition($selectedEdition) : collect();

        $filename = 'kiu-medal-tally-' . ($selectedEdition?->year ?? date('Y')) . '.csv';

        return response()->streamDownload(function () use ($medalTally) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Microsoft Excel reads text properly
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Rank',
                'Department Name',
                'Department Code',
                'Gold Medals (5 pts)',
                'Silver Medals (3 pts)',
                'Bronze Medals (1 pt)',
                'Total Medals',
                'Total Points',
            ]);

            foreach ($medalTally as $row) {
                fputcsv($handle, [
                    $row['rank'],
                    $row['department']->name,
                    $row['department']->code ?? '',
                    $row['gold'],
                    $row['silver'],
                    $row['bronze'],
                    $row['total_medals'],
                    $row['points'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export Official Winners & Honors as CSV.
     */
    public function exportWinners(Request $request): StreamedResponse
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $selectedEditionId = $request->input('edition_id') ?? $editions->first()?->id;
        $selectedEdition = $selectedEditionId ? Edition::find($selectedEditionId) : null;

        $winners = collect();
        if ($selectedEdition) {
            $winners = Winner::with(['sport', 'winnerDepartment', 'runnerUpDepartment', 'secondRunnerUpDepartment'])
                ->where('edition_id', $selectedEdition->id)
                ->get()
                ->sortBy(fn($w) => $w->sport->name ?? '');
        }

        $filename = 'kiu-winners-' . ($selectedEdition?->year ?? date('Y')) . '.csv';

        return response()->streamDownload(function () use ($winners, $selectedEdition) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Sport Discipline',
                'Category',
                'Gold Medalist (Champion)',
                'Silver Medalist (Runner Up)',
                'Bronze Medalist (3rd Place)',
                'Final Score / Result',
                'Edition',
            ]);

            foreach ($winners as $w) {
                fputcsv($handle, [
                    $w->sport?->name ?? 'N/A',
                    ucfirst($w->sport?->category ?? ''),
                    $w->winnerDepartment?->name ?? 'N/A',
                    $w->runnerUpDepartment?->name ?? 'N/A',
                    $w->secondRunnerUpDepartment?->name ?? 'N/A',
                    $w->final_score ?? '',
                    $selectedEdition?->title ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export Fixtures & Results as CSV.
     */
    public function exportFixtures(Request $request): StreamedResponse
    {
        $selectedDate = $request->input('date');
        $selectedEditionId = $request->input('edition_id');

        $query = Fixture::with([
            'sport',
            'team1.department',
            'team2.department',
            'venue',
            'edition',
            'result.winnerTeam',
            'result.playerOfTheMatch',
        ])->orderBy('match_date', 'asc')->orderBy('match_time', 'asc');

        if ($selectedDate) {
            $query->whereDate('match_date', $selectedDate);
        }
        if ($selectedEditionId) {
            $query->where('edition_id', $selectedEditionId);
        }

        $fixtures = $query->get();
        $filename = 'kiu-fixtures-' . ($selectedDate ?? 'schedule') . '.csv';

        return response()->streamDownload(function () use ($fixtures) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Match ID',
                'Edition',
                'Sport',
                'Category',
                'Round',
                'Date',
                'Time',
                'Venue',
                'Status',
                'Team 1 (Home)',
                'Team 1 Department',
                'Team 1 Score',
                'Team 2 (Away)',
                'Team 2 Department',
                'Team 2 Score',
                'Winner Team',
                'Player of the Match',
                'Summary',
                'Live Stream URL',
            ]);

            foreach ($fixtures as $f) {
                fputcsv($handle, [
                    $f->id,
                    $f->edition?->title ?? '',
                    $f->sport?->name ?? '',
                    ucfirst($f->sport?->category ?? ''),
                    $f->round ?? '',
                    $f->match_date ? $f->match_date->format('Y-m-d') : '',
                    $f->match_time ? substr($f->match_time, 0, 5) : '',
                    $f->venue?->name ?? '',
                    strtoupper($f->status),
                    $f->team1?->name ?? '',
                    $f->team1?->department?->name ?? '',
                    $f->result?->team1_score ?? '',
                    $f->team2?->name ?? '',
                    $f->team2?->department?->name ?? '',
                    $f->result?->team2_score ?? '',
                    $f->result?->winnerTeam?->name ?? ($f->status === 'completed' && !$f->result?->winner_team_id ? 'Draw' : ''),
                    $f->result?->playerOfTheMatch?->name ?? '',
                    $f->result?->summary ?? '',
                    $f->stream_url ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export Department Squad Accreditation & Player Lists as CSV.
     */
    public function exportTeams(Request $request): StreamedResponse
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $selectedEditionId = $request->input('edition_id') ?? $editions->first()?->id;
        $selectedSportId = $request->input('sport_id');

        $query = Team::with(['edition', 'sport', 'department', 'players'])
            ->orderBy('name');

        if ($selectedEditionId) {
            $query->where('edition_id', $selectedEditionId);
        }
        if ($selectedSportId) {
            $query->where('sport_id', $selectedSportId);
        }

        $teams = $query->get();
        $filename = 'kiu-squad-accreditation-' . date('Ymd') . '.csv';

        return response()->streamDownload(function () use ($teams) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Edition',
                'Sport',
                'Sport Category',
                'Department',
                'Team Name',
                'Player Name',
                'Registration Number',
                'Digital Pass URL',
            ]);

            foreach ($teams as $team) {
                if ($team->players->isEmpty()) {
                    fputcsv($handle, [
                        $team->edition?->title ?? '',
                        $team->sport?->name ?? '',
                        ucfirst($team->sport?->category ?? ''),
                        $team->department?->name ?? '',
                        $team->name,
                        '(No players registered)',
                        '',
                        '',
                    ]);
                } else {
                    foreach ($team->players as $p) {
                        fputcsv($handle, [
                            $team->edition?->title ?? '',
                            $team->sport?->name ?? '',
                            ucfirst($team->sport?->category ?? ''),
                            $team->department?->name ?? '',
                            $team->name,
                            $p->name,
                            $p->reg_no ?? '',
                            route('players.card', $p),
                        ]);
                    }
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}

