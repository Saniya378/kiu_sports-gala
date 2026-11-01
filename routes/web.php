<?php

use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EditionController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\SportController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
});

require __DIR__.'/auth.php';

