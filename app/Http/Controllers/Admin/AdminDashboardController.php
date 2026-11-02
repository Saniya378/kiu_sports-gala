<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\News;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the official Admin Control Dashboard.
     */
    public function index(): View
    {
        $runningEdition = Edition::where('status', 'running')->first();
        $latestEdition = $runningEdition ?? Edition::orderBy('year', 'desc')->first();

        $stats = [
            'editions' => Edition::count(),
            'departments' => Department::count(),
            'sports' => Sport::count(),
            'teams' => Team::count(),
            'players' => Player::count(),
            'fixtures' => Fixture::count(),
            'completedFixtures' => Fixture::where('status', 'completed')->count(),
            'news' => News::count(),
        ];

        // Today's fixtures
        $todayFixtures = collect();
        if ($latestEdition) {
            $todayFixtures = Fixture::with(['sport', 'team1.department', 'team2.department', 'venue', 'result'])
                ->where('edition_id', $latestEdition->id)
                ->whereDate('match_date', Carbon::today())
                ->orderBy('match_time', 'asc')
                ->get();
        }

        // Recent Completed Results
        $recentResults = Fixture::with(['sport', 'team1.department', 'team2.department', 'venue', 'result.winnerTeam'])
            ->whereHas('result')
            ->orderBy('match_date', 'desc')
            ->latest('id')
            ->take(5)
            ->get();

        // Recent News
        $recentNews = News::with('edition')->latest('published_at')->take(4)->get();

        return view('admin.dashboard', compact(
            'latestEdition',
            'runningEdition',
            'stats',
            'todayFixtures',
            'recentResults',
            'recentNews'
        ));
    }
}
