<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\EditionPhoto;
use App\Models\Fixture;
use App\Models\MatchPhoto;
use App\Models\News;
use App\Models\Sport;
use App\Models\Team;
use App\Models\Winner;
use App\Services\BracketService;
use App\Services\MedalTallyService;
use App\Services\PointsTableService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Display the Public Home page.
     * Shows latest edition banner. If running, shows today's matches.
     * Else shows "Next gala coming soon" and highlights of the latest completed edition.
     */
    public function home(MedalTallyService $medalTallyService): View
    {
        $latestEdition = Edition::orderBy('year', 'desc')->first();

        $todayMatches = collect();
        $completedEdition = null;
        $topMedalTally = $latestEdition ? $medalTallyService->calculateForEdition($latestEdition)->take(3) : collect();

        if ($latestEdition && $latestEdition->status === 'running') {
            $todayMatches = Fixture::with(['sport', 'team1.department', 'team2.department', 'venue', 'result'])
                ->where('edition_id', $latestEdition->id)
                ->whereDate('match_date', Carbon::today())
                ->orderBy('match_time', 'asc')
                ->get();
        } else {
            // Find the most recent completed edition for highlights
            $completedEdition = Edition::with([
                'winners.sport',
                'winners.winnerDepartment',
                'winners.runnerUpDepartment',
                'photos',
            ])
            ->where('status', 'completed')
            ->orderBy('year', 'desc')
            ->first();
        }

        return view('public.home', compact('latestEdition', 'todayMatches', 'completedEdition', 'topMedalTally'));
    }

    /**
     * Display Today's Matches.
     * Shows only fixtures of the running edition with today's date, with sport filter.
     */
    public function today(Request $request): View
    {
        $runningEdition = Edition::where('status', 'running')->first();

        $fixtures = collect();
        $sports = collect();

        if ($runningEdition) {
            $query = Fixture::with(['sport', 'team1.department', 'team2.department', 'venue', 'result', 'photos'])
                ->where('edition_id', $runningEdition->id)
                ->whereDate('match_date', Carbon::today())
                ->orderBy('match_time', 'asc');

            if ($request->filled('sport_id')) {
                $query->where('sport_id', $request->sport_id);
            }

            $fixtures = $query->get();

            // Sports that have matches scheduled today, or all sports
            $sports = Sport::whereHas('fixtures', function ($q) use ($runningEdition) {
                $q->where('edition_id', $runningEdition->id)
                  ->whereDate('match_date', Carbon::today());
            })->orderBy('name')->get();

            if ($sports->isEmpty()) {
                $sports = Sport::orderBy('name')->get();
            }
        }

        return view('public.today', compact('runningEdition', 'fixtures', 'sports'));
    }

    /**
     * Display the Schedule of an edition.
     * Shows all fixtures of an edition, with edition selector (defaulting to latest),
     * and filters by sport and date.
     */
    public function schedule(Request $request): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $selectedEditionId = $request->input('edition_id') ?? $editions->first()?->id;

        $selectedEdition = $selectedEditionId ? Edition::find($selectedEditionId) : null;
        $sports = Sport::orderBy('name')->get();

        $fixtures = collect();
        if ($selectedEdition) {
            $query = Fixture::with(['sport', 'team1.department', 'team2.department', 'venue', 'result'])
                ->where('edition_id', $selectedEdition->id)
                ->orderBy('match_date', 'asc')
                ->orderBy('match_time', 'asc');

            if ($request->filled('sport_id')) {
                $query->where('sport_id', $request->sport_id);
            }

            if ($request->filled('date')) {
                $query->whereDate('match_date', $request->date);
            }

            $fixtures = $query->get();
        }

        return view('public.schedule', compact('editions', 'selectedEdition', 'sports', 'fixtures'));
    }

    /**
     * Display Tournament Knockout Bracket Tree.
     * Select edition and sport to view quarter finals, semi finals, final and champion.
     */
    public function bracket(Request $request, BracketService $bracketService): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $selectedEditionId = $request->input('edition_id') ?? $editions->firstWhere('status', 'running')?->id ?? $editions->first()?->id;
        $selectedEdition = $selectedEditionId ? Edition::find($selectedEditionId) : null;

        $sports = Sport::orderBy('name')->get();
        $selectedSportId = $request->input('sport_id') ?? $sports->first()?->id;
        $selectedSport = $selectedSportId ? Sport::find($selectedSportId) : null;

        $bracketData = [
            'has_knockout' => false,
            'rounds' => collect(),
            'champion' => null,
            'total_matches' => 0,
        ];

        if ($selectedEdition && $selectedSport) {
            $bracketData = $bracketService->getBracket($selectedEdition, $selectedSport);
        }

        return view('public.bracket', compact('editions', 'selectedEdition', 'sports', 'selectedSport', 'bracketData'));
    }

    /**
     * Display Match Detail.
     * Shows teams, scores, venue, round, lineups/players, and match photos grid.
     */
    public function matchDetail(Fixture $fixture): View
    {
        $fixture->load([
            'edition',
            'sport',
            'team1.department',
            'team1.players',
            'team2.department',
            'team2.players',
            'venue',
            'result.winnerTeam',
            'result.playerOfTheMatch.team.department',
            'photos.uploader',
            'events.team',
            'events.player',
        ]);

        return view('public.match-detail', compact('fixture'));
    }

    /**
     * Display Results and Points Table per edition and sport.
     * Uses an edition selector that defaults to the latest edition.
     */
    public function results(Request $request, PointsTableService $pointsTableService, MedalTallyService $medalTallyService): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $selectedEditionId = $request->input('edition_id') ?? $editions->first()?->id;
        $selectedEdition = $selectedEditionId ? Edition::find($selectedEditionId) : null;

        $sports = Sport::orderBy('name')->get();
        $selectedSportId = $request->input('sport_id') ?? $sports->first()?->id;
        $selectedSport = $selectedSportId ? Sport::find($selectedSportId) : null;

        $completedFixtures = collect();
        $standings = collect();
        $medalTally = $selectedEdition ? $medalTallyService->calculateForEdition($selectedEdition) : collect();

        if ($selectedEdition && $selectedSport) {
            // All completed fixtures with results for selected edition & sport
            $completedFixtures = Fixture::with([
                'team1.department',
                'team2.department',
                'venue',
                'result.winnerTeam',
                'result.playerOfTheMatch',
                'photos',
            ])
            ->where('edition_id', $selectedEdition->id)
            ->where('sport_id', $selectedSport->id)
            ->whereHas('result')
            ->orderBy('match_date', 'desc')
            ->latest('id')
            ->get();

            // Dynamic points table calculation
            $standings = $pointsTableService->calculate($selectedEdition, $selectedSport);
        }

        return view('public.results', compact(
            'editions',
            'selectedEdition',
            'sports',
            'selectedSport',
            'completedFixtures',
            'standings',
            'medalTally'
        ));
    }

    /**
     * Display Archive for past/selected gala editions.
     * Select year, show that edition's sports, winners, results, and photos.
     */
    public function archive(Request $request): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();

        // Select year: by query param 'year' or 'edition_id', or default to latest completed, or latest
        $selectedEdition = null;
        if ($request->filled('year')) {
            $selectedEdition = $editions->firstWhere('year', (int) $request->year);
        } elseif ($request->filled('edition_id')) {
            $selectedEdition = $editions->firstWhere('id', (int) $request->edition_id);
        }

        if (! $selectedEdition) {
            $selectedEdition = $editions->firstWhere('status', 'completed') ?? $editions->first();
        }

        $sports = collect();
        $winners = collect();
        $results = collect();
        $editionPhotos = collect();
        $matchPhotos = collect();

        if ($selectedEdition) {
            // Find sports that participated in this edition
            $sportIds = collect();
            $sportIds = $sportIds->merge(Team::where('edition_id', $selectedEdition->id)->pluck('sport_id'));
            $sportIds = $sportIds->merge(Fixture::where('edition_id', $selectedEdition->id)->pluck('sport_id'));
            $sportIds = $sportIds->merge(Winner::where('edition_id', $selectedEdition->id)->pluck('sport_id'));

            $sports = Sport::whereIn('id', $sportIds->unique()->filter())->orderBy('name')->get();
            if ($sports->isEmpty()) {
                $sports = Sport::orderBy('name')->get();
            }

            // Winners from winners table
            $winners = Winner::with(['sport', 'winnerDepartment', 'runnerUpDepartment'])
                ->where('edition_id', $selectedEdition->id)
                ->get();

            // Completed fixtures with results for that edition
            $results = Fixture::with([
                'sport',
                'team1.department',
                'team2.department',
                'venue',
                'result.winnerTeam',
            ])
            ->where('edition_id', $selectedEdition->id)
            ->whereHas('result')
            ->orderBy('match_date', 'asc')
            ->orderBy('match_time', 'asc')
            ->get();

            // Edition photos
            $editionPhotos = EditionPhoto::with('sport')
                ->where('edition_id', $selectedEdition->id)
                ->latest('id')
                ->get();

            // Match photos from fixtures of that edition
            $matchPhotos = MatchPhoto::with(['fixture.sport', 'fixture.team1', 'fixture.team2'])
                ->whereHas('fixture', function ($q) use ($selectedEdition) {
                    $q->where('edition_id', $selectedEdition->id);
                })
                ->latest('id')
                ->get();
        }

        return view('public.archive', compact(
            'editions',
            'selectedEdition',
            'sports',
            'winners',
            'results',
            'editionPhotos',
            'matchPhotos'
        ));
    }

    /**
     * Display Hall of Fame.
     * Per sport, lists all winners and runner-ups across all editions from the winners table.
     */
    public function hallOfFame(Request $request): View
    {
        // Fetch all winners across editions with relationships
        $allWinners = Winner::with(['edition', 'sport', 'winnerDepartment', 'runnerUpDepartment'])
            ->get()
            ->sortByDesc(fn($w) => $w->edition->year ?? 0);

        // Fetch sports that have winners records
        $sportsWithWinners = Sport::whereHas('winners')->orderBy('name')->get();
        $allSports = Sport::orderBy('name')->get();

        // Group winners by sport_id
        $winnersBySport = $allWinners->groupBy('sport_id');

        // Calculate all-time medals by department
        $departmentTallies = [];
        foreach ($allWinners as $winner) {
            if ($winner->winnerDepartment) {
                $id = $winner->winner_department_id;
                if (! isset($departmentTallies[$id])) {
                    $departmentTallies[$id] = [
                        'department' => $winner->winnerDepartment,
                        'gold' => 0,
                        'silver' => 0,
                        'total' => 0,
                    ];
                }
                $departmentTallies[$id]['gold']++;
                $departmentTallies[$id]['total']++;
            }

            if ($winner->runnerUpDepartment) {
                $id = $winner->runner_up_department_id;
                if (! isset($departmentTallies[$id])) {
                    $departmentTallies[$id] = [
                        'department' => $winner->runnerUpDepartment,
                        'gold' => 0,
                        'silver' => 0,
                        'total' => 0,
                    ];
                }
                $departmentTallies[$id]['silver']++;
                $departmentTallies[$id]['total']++;
            }
        }

        $allTimeLeaderboard = collect($departmentTallies)->sort(function ($a, $b) {
            if ($a['gold'] !== $b['gold']) {
                return $b['gold'] <=> $a['gold'];
            }
            if ($a['silver'] !== $b['silver']) {
                return $b['silver'] <=> $a['silver'];
            }
            return strcmp($a['department']->name, $b['department']->name);
        })->values();

        return view('public.hall-of-fame', compact(
            'sportsWithWinners',
            'allSports',
            'winnersBySport',
            'allTimeLeaderboard'
        ));
    }

    /**
     * Display Photo Gallery.
     * Shows edition_photos and match_photos grouped by edition, with lightbox.
     */
    public function gallery(Request $request): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $selectedYear = $request->input('year');

        $editionPhotos = EditionPhoto::with(['edition', 'sport'])->latest('id')->get();
        $matchPhotos = MatchPhoto::with(['fixture.edition', 'fixture.sport', 'fixture.team1', 'fixture.team2'])->latest('id')->get();

        // Build collection grouped by edition
        $galleryByEdition = [];

        foreach ($editions as $edition) {
            if ($selectedYear && (int) $selectedYear !== (int) $edition->year) {
                continue;
            }

            $items = collect();

            // Edition photos
            foreach ($editionPhotos->where('edition_id', $edition->id) as $ep) {
                $items->push([
                    'id' => 'ep-' . $ep->id,
                    'image_url' => $ep->image_url,
                    'caption' => $ep->caption ?: ($edition->title . ' - Official Photo'),
                    'tag' => $ep->sport ? $ep->sport->name : 'Gala Event',
                    'type' => 'Edition Photo',
                    'edition_title' => $edition->title,
                    'edition_year' => $edition->year,
                    'created_at' => $ep->created_at,
                ]);
            }

            // Match photos
            foreach ($matchPhotos as $mp) {
                if ($mp->fixture && $mp->fixture->edition_id === $edition->id) {
                    $matchTitle = ($mp->fixture->team1->name ?? 'Team 1') . ' vs ' . ($mp->fixture->team2->name ?? 'Team 2');
                    $items->push([
                        'id' => 'mp-' . $mp->id,
                        'image_url' => $mp->image_url,
                        'caption' => $mp->caption ?: $matchTitle,
                        'tag' => $mp->fixture->sport ? $mp->fixture->sport->name : 'Match Action',
                        'type' => 'Match Action',
                        'edition_title' => $edition->title,
                        'edition_year' => $edition->year,
                        'created_at' => $mp->created_at,
                    ]);
                }
            }

            if ($items->isNotEmpty()) {
                $galleryByEdition[$edition->id] = [
                    'edition' => $edition,
                    'photos' => $items->sortByDesc('created_at')->values(),
                ];
            }
        }

        return view('public.gallery', compact(
            'editions',
            'selectedYear',
            'galleryByEdition'
        ));
    }

    /**
     * Display Public News & Bulletins listing.
     */
    public function news(Request $request): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();

        $query = News::with('edition')->published()->latest('published_at');

        if ($request->filled('edition_id')) {
            $query->where('edition_id', $request->edition_id);
        }

        $newsList = $query->paginate(9)->withQueryString();

        return view('public.news.index', compact('newsList', 'editions'));
    }

    /**
     * Display Public News Detail page.
     */
    public function newsShow(News $news): View
    {
        $news->load('edition');

        $recentNews = News::with('edition')
            ->published()
            ->where('id', '!=', $news->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('public.news.show', compact('news', 'recentNews'));
    }

    /**
     * Display Participating Teams and Squad Rosters.
     */
    public function teams(Request $request): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $selectedEditionId = $request->input('edition_id') ?? $editions->first()?->id;
        $selectedEdition = $selectedEditionId ? Edition::find($selectedEditionId) : null;

        $sports = Sport::orderBy('name')->get();
        $selectedSportId = $request->input('sport_id');

        $teams = collect();
        if ($selectedEdition) {
            $query = Team::with(['sport', 'department', 'players'])
                ->where('edition_id', $selectedEdition->id)
                ->where('status', 'approved')
                ->orderBy('name');

            if ($selectedSportId) {
                $query->where('sport_id', $selectedSportId);
            }

            $teams = $query->get();
        }

        return view('public.teams', compact('editions', 'selectedEdition', 'sports', 'selectedSportId', 'teams'));
    }

    /**
     * Display About & Contact page with static content.
     */
    public function about(): View
    {
        $latestEdition = Edition::orderBy('year', 'desc')->first();

        return view('public.about', compact('latestEdition'));
    }
}

