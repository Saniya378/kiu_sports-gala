<?php

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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// Public Pages
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/today', [PublicController::class, 'today'])->name('today');
Route::get('/schedule', [PublicController::class, 'schedule'])->name('schedule');
Route::get('/matches/{fixture}', [PublicController::class, 'matchDetail'])->name('matches.show');
Route::get('/results', [PublicController::class, 'results'])->name('results');
Route::get('/archive', [PublicController::class, 'archive'])->name('archive');
Route::get('/hall-of-fame', [PublicController::class, 'hallOfFame'])->name('hall-of-fame');
Route::get('/gallery', [PublicController::class, 'gallery'])->name('gallery');
Route::get('/news', [PublicController::class, 'news'])->name('news');
Route::get('/news/{news}', [PublicController::class, 'newsShow'])->name('news.show');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/contact', [PublicController::class, 'about'])->name('contact');

// Convenient aliases
Route::get('/matches/today', [PublicController::class, 'today'])->name('matches.today');
Route::get('/fixtures/{fixture}', [PublicController::class, 'matchDetail'])->name('fixtures.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
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
    Route::get('reports/winners', [ReportController::class, 'winners'])->name('reports.winners');
    Route::get('reports/fixtures', [ReportController::class, 'fixtures'])->name('reports.fixtures');
    Route::get('reports/teams', [ReportController::class, 'teams'])->name('reports.teams');
});

// Score Entry (Accessible by Admin and Scorer)
Route::middleware(['auth', 'can.score'])->group(function () {
    Route::get('/admin/score-entry', [ScoreEntryController::class, 'index'])->name('admin.score-entry.index');
    Route::post('/admin/score-entry', [ScoreEntryController::class, 'store'])->name('admin.score-entry.store');

    Route::get('/score-entry', [ScoreEntryController::class, 'index'])->name('score-entry.index');
    Route::post('/score-entry', [ScoreEntryController::class, 'store'])->name('score-entry.store');

    // Match Photos (Accessible by Admin and Scorer)
    Route::post('/admin/fixtures/{fixture}/photos', [MatchPhotoController::class, 'store'])->name('admin.fixtures.photos.store');
    Route::delete('/admin/match-photos/{photo}', [MatchPhotoController::class, 'destroy'])->name('admin.fixtures.photos.destroy');
});

require __DIR__.'/auth.php';

