<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EditionController;
use App\Http\Controllers\Admin\EditionPhotoController;
use App\Http\Controllers\Admin\FixtureController;
use App\Http\Controllers\Admin\MatchPhotoController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\PointsTableController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScoreEntryController;
use App\Http\Controllers\Admin\SportController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\Admin\WinnerController;
use App\Http\Controllers\LiveScoreController;
use App\Http\Controllers\PlayerPassController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// Live Scores API (Polling & Real-time updates)
Route::get('/api/live-scores', [LiveScoreController::class, 'index'])->name('api.live-scores.index');
Route::get('/api/matches/{fixture}/live', [LiveScoreController::class, 'show'])->name('api.live-scores.show');

// Player Digital Pass & Accreditation Verification
Route::get('/players/{player}/card', [PlayerPassController::class, 'show'])->name('players.card');
Route::get('/verify/player/{player}', [PlayerPassController::class, 'show'])->name('players.verify');
Route::get('/teams/{team}/id-cards', [PlayerPassController::class, 'teamCards'])->name('teams.id-cards');

// Public Pages
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/today', [PublicController::class, 'today'])->name('today');
Route::get('/schedule', [PublicController::class, 'schedule'])->name('schedule');
Route::get('/bracket', [PublicController::class, 'bracket'])->name('bracket');
Route::get('/matches/{fixture}', [PublicController::class, 'matchDetail'])->name('matches.show');
Route::get('/results', [PublicController::class, 'results'])->name('results');
Route::get('/archive', [PublicController::class, 'archive'])->name('archive');
Route::get('/hall-of-fame', [PublicController::class, 'hallOfFame'])->name('hall-of-fame');
Route::get('/gallery', [PublicController::class, 'gallery'])->name('gallery');
Route::get('/news', [PublicController::class, 'news'])->name('news');
Route::get('/news/{news}', [PublicController::class, 'newsShow'])->name('news.show');
Route::get('/teams', [PublicController::class, 'teams'])->name('teams.public');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/contact', [PublicController::class, 'about'])->name('contact');

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

    // Teams & Players
    Route::post('teams/copy', [TeamController::class, 'copyFromEdition'])->name('teams.copy');
    Route::resource('teams', TeamController::class);
    Route::post('teams/{team}/players', [PlayerController::class, 'store'])->name('teams.players.store');
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
    Route::get('reports/winners', [ReportController::class, 'winners'])->name('reports.winners');
    Route::get('reports/fixtures', [ReportController::class, 'fixtures'])->name('reports.fixtures');
    Route::get('reports/teams', [ReportController::class, 'teams'])->name('reports.teams');
});

// Score Entry (Accessible by Admin and Scorer)
Route::middleware(['auth', 'can.score'])->group(function () {
    Route::get('/admin/score-entry', [ScoreEntryController::class, 'index'])->name('admin.score-entry.index');
    Route::get('/admin/score-entry/pad/{fixture}', [ScoreEntryController::class, 'quickPad'])->name('admin.score-entry.pad');
    Route::post('/admin/score-entry', [ScoreEntryController::class, 'store'])->name('admin.score-entry.store');

    Route::get('/score-entry', [ScoreEntryController::class, 'index'])->name('score-entry.index');
    Route::get('/score-entry/pad/{fixture}', [ScoreEntryController::class, 'quickPad'])->name('score-entry.pad');
    Route::post('/score-entry', [ScoreEntryController::class, 'store'])->name('score-entry.store');

    // Match Photos (Accessible by Admin and Scorer)
    Route::post('/admin/fixtures/{fixture}/photos', [MatchPhotoController::class, 'store'])->name('admin.fixtures.photos.store');
    Route::delete('/admin/match-photos/{photo}', [MatchPhotoController::class, 'destroy'])->name('admin.fixtures.photos.destroy');
});

require __DIR__.'/auth.php';

