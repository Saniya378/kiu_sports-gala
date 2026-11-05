<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EditionController;
use App\Http\Controllers\Admin\EditionPhotoController;
use App\Http\Controllers\Admin\FixtureController;
use App\Http\Controllers\Admin\MatchEventController;
use App\Http\Controllers\Admin\MatchPhotoController;
use App\Http\Controllers\Admin\MatchVideoController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\PointsTableController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScoreEntryController;
use App\Http\Controllers\Admin\SportController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\Admin\WinnerController;
use App\Http\Controllers\CeremonyController;
use App\Http\Controllers\Coordinator\CoordinatorTeamController;
use App\Http\Controllers\Coordinator\CoordinatorTrialController;
use App\Http\Controllers\FanEngagementController;
use App\Http\Controllers\LiveScoreController;
use App\Http\Controllers\PlayerPassController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\StudentTrialController;
use Illuminate\Support\Facades\Route;

// PWA Manifest
Route::get('/manifest.json', function () {
    return response()->json(json_decode(file_get_contents(public_path('manifest.json')), true));
});

// Locale Switcher (English / Urdu)
Route::get('/locale/{lang}', function (string $lang) {
    if (in_array($lang, ['en', 'ur'])) {
        session(['locale' => $lang]);
    }
    return back();
})->name('locale.switch');

// Live Scores API & Real-time Server-Sent Events (SSE) Stream (Throttled for high load)
Route::middleware('throttle:120,1')->group(function () {
    Route::get('/api/live-scores', [LiveScoreController::class, 'index'])->name('api.live-scores.index');
    Route::get('/api/live-scores/stream', [LiveScoreController::class, 'streamAll'])->name('api.live-scores.stream');
    Route::get('/api/matches/{fixture}/live', [LiveScoreController::class, 'show'])->name('api.live-scores.show');
    Route::get('/api/matches/{fixture}/stream', [LiveScoreController::class, 'streamMatch'])->name('api.live-scores.stream.match');
});

// Department Follow & Match Notifications
Route::post('/api/follow-department', [PublicController::class, 'followDepartment'])->name('api.department.follow');
Route::get('/api/followed-department', [PublicController::class, 'getFollowedDepartment'])->name('api.department.followed');

// Fan Engagement & Spectator Reactions (Throttled against click-spam & tampering)
Route::post('/matches/{fixture}/vote', [FanEngagementController::class, 'vote'])->middleware('throttle:20,1')->name('matches.vote');
Route::post('/matches/{fixture}/react', [FanEngagementController::class, 'react'])->middleware('throttle:60,1')->name('matches.react');
Route::get('/matches/{fixture}/fan-stats', [FanEngagementController::class, 'stats'])->middleware('throttle:120,1')->name('matches.fan-stats');

// Player Digital Pass & Accreditation Verification
Route::get('/players/{player}/card', [PlayerPassController::class, 'show'])->name('players.card');
Route::get('/verify/player/{player}', [PlayerPassController::class, 'show'])->name('players.verify');
Route::get('/teams/{team}/id-cards', [PlayerPassController::class, 'teamCards'])->name('teams.id-cards');

// Student Trials & Tryouts Registration
Route::get('/trials', [StudentTrialController::class, 'create'])->name('trials.create');
Route::post('/trials', [StudentTrialController::class, 'store'])->name('trials.store');
Route::get('/trials/{trial}/slip', [StudentTrialController::class, 'slip'])->name('trials.slip');

// Public Pages
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/today', [PublicController::class, 'today'])->name('today');
Route::get('/schedule', [PublicController::class, 'schedule'])->name('schedule');
Route::get('/bracket', [PublicController::class, 'bracket'])->name('bracket');
Route::get('/leaderboard', [PublicController::class, 'leaderboard'])->name('leaderboard');
Route::get('/players/{player}', [PublicController::class, 'playerProfile'])->name('players.profile');
Route::get('/matches/{fixture}', [PublicController::class, 'matchDetail'])->name('matches.show');
Route::get('/results', [PublicController::class, 'results'])->name('results');
Route::get('/archive', [PublicController::class, 'archive'])->name('archive');
Route::get('/hall-of-fame', [PublicController::class, 'hallOfFame'])->name('hall-of-fame');
Route::get('/gallery', [PublicController::class, 'gallery'])->name('gallery');
Route::get('/news', [PublicController::class, 'news'])->name('news');
Route::get('/news/{news}', [PublicController::class, 'newsShow'])->name('news.show');
Route::get('/teams', [PublicController::class, 'teams'])->name('teams.public');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');

// Closing Ceremony & Auditorium Big Screen Mode
Route::get('/ceremony', [CeremonyController::class, 'index'])->name('ceremony');
Route::get('/presentation', [CeremonyController::class, 'index'])->name('presentation');

// Convenient aliases
Route::get('/matches/today', [PublicController::class, 'today'])->name('matches.today');
Route::get('/fixtures/{fixture}', [PublicController::class, 'matchDetail'])->name('fixtures.show');

Route::get('/dashboard', function () {
    if (auth()->user()?->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if (auth()->user()?->role === 'scorer') {
        return redirect()->route('admin.score-entry.index');
    }

    if (auth()->user()?->role === 'coordinator') {
        return redirect()->route('coordinator.teams.index');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard', [AdminDashboardController::class, 'index']);

    Route::resource('editions', EditionController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('venues', VenueController::class);
    Route::resource('sports', SportController::class);
    Route::resource('users', UserController::class);

    // Teams & Players
    Route::post('teams/copy', [TeamController::class, 'copyFromEdition'])->name('teams.copy');
    Route::get('teams/players/sample-csv', [PlayerController::class, 'sampleCsv'])->name('teams.players.sample-csv');
    Route::resource('teams', TeamController::class);
    Route::post('teams/{team}/approve', [TeamController::class, 'approve'])->name('teams.approve');
    Route::post('teams/{team}/reject', [TeamController::class, 'reject'])->name('teams.reject');
    Route::post('teams/{team}/players', [PlayerController::class, 'store'])->name('teams.players.store');
    Route::post('teams/{team}/players/import', [PlayerController::class, 'importCsv'])->name('teams.players.import');
    Route::delete('teams/{team}/players/{player}', [PlayerController::class, 'destroy'])->name('teams.players.destroy');

    // Fixtures
    Route::resource('fixtures', FixtureController::class);

    // Points Table / Standings
    Route::get('points-table', [PointsTableController::class, 'index'])->name('points-table.index');

    // Winners & Archive Records (Admin only)
    Route::resource('winners', WinnerController::class);

    // Edition Photos (Admin only)
    Route::post('editions/{edition}/photos', [EditionPhotoController::class, 'store'])->name('editions.photos.store');
    Route::delete('edition-photos/{photo}', [EditionPhotoController::class, 'destroy'])->name('editions.photos.destroy');

    // News CRUD (Admin only)
    Route::resource('news', NewsController::class);

    // Reports (Admin only)
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/medals', [ReportController::class, 'medals'])->name('reports.medals');
    Route::get('reports/medals/export', [ReportController::class, 'exportMedals'])->name('reports.medals.export');
    Route::get('reports/winners', [ReportController::class, 'winners'])->name('reports.winners');
    Route::get('reports/winners/export', [ReportController::class, 'exportWinners'])->name('reports.winners.export');
    Route::get('reports/fixtures', [ReportController::class, 'fixtures'])->name('reports.fixtures');
    Route::get('reports/fixtures/export', [ReportController::class, 'exportFixtures'])->name('reports.fixtures.export');
    Route::get('reports/teams', [ReportController::class, 'teams'])->name('reports.teams');
    Route::get('reports/teams/export', [ReportController::class, 'exportTeams'])->name('reports.teams.export');
});

// Department Sports Coordinator Portal (Squad Submissions & Player Rosters)
Route::middleware(['auth', 'coordinator'])->prefix('coordinator')->name('coordinator.')->group(function () {
    Route::get('/', [CoordinatorTeamController::class, 'index'])->name('dashboard');
    Route::get('teams', [CoordinatorTeamController::class, 'index'])->name('teams.index');
    Route::get('teams/create', [CoordinatorTeamController::class, 'create'])->name('teams.create');
    Route::post('teams', [CoordinatorTeamController::class, 'store'])->name('teams.store');
    Route::get('teams/{team}', [CoordinatorTeamController::class, 'show'])->name('teams.show');
    Route::post('teams/{team}/players', [CoordinatorTeamController::class, 'storePlayer'])->name('teams.players.store');
    Route::post('teams/{team}/players/import', [CoordinatorTeamController::class, 'importCsv'])->name('teams.players.import');
    Route::delete('teams/{team}/players/{player}', [CoordinatorTeamController::class, 'destroyPlayer'])->name('teams.players.destroy');
    Route::post('teams/{team}/resubmit', [CoordinatorTeamController::class, 'resubmit'])->name('teams.resubmit');

    // Department Trials Management
    Route::get('trials', [CoordinatorTrialController::class, 'index'])->name('trials.index');
    Route::patch('trials/{trial}/status', [CoordinatorTrialController::class, 'updateStatus'])->name('trials.status');
    Route::post('trials/{trial}/draft', [CoordinatorTrialController::class, 'draftToSquad'])->name('trials.draft');
});

// Score Entry (Accessible by Admin and Scorer)
Route::middleware(['auth', 'can.score'])->group(function () {
    Route::get('/admin/score-entry', [ScoreEntryController::class, 'index'])->name('admin.score-entry.index');
    Route::get('/admin/score-entry/pad/{fixture}', [ScoreEntryController::class, 'quickPad'])->name('admin.score-entry.pad');
    Route::post('/admin/score-entry', [ScoreEntryController::class, 'store'])->name('admin.score-entry.store');

    Route::get('/score-entry', [ScoreEntryController::class, 'index'])->name('score-entry.index');
    Route::get('/score-entry/pad/{fixture}', [ScoreEntryController::class, 'quickPad'])->name('score-entry.pad');
    Route::post('/score-entry', [ScoreEntryController::class, 'store'])->name('score-entry.store');

    // Match Photos & Video Highlights (Accessible by Admin and Scorer)
    Route::post('/admin/fixtures/{fixture}/photos', [MatchPhotoController::class, 'store'])->name('admin.fixtures.photos.store');
    Route::delete('/admin/match-photos/{photo}', [MatchPhotoController::class, 'destroy'])->name('admin.fixtures.photos.destroy');
    Route::post('/admin/fixtures/{fixture}/videos', [MatchVideoController::class, 'store'])->name('admin.fixtures.videos.store');
    Route::delete('/admin/match-videos/{video}', [MatchVideoController::class, 'destroy'])->name('admin.fixtures.videos.destroy');

    // Match Timeline Events (Accessible by Admin and Scorer)
    Route::post('/admin/fixtures/{fixture}/events', [MatchEventController::class, 'store'])->name('admin.fixtures.events.store');
    Route::delete('/admin/fixtures/{fixture}/events/{event}', [MatchEventController::class, 'destroy'])->name('admin.fixtures.events.destroy');
});

require __DIR__.'/auth.php';

