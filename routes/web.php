<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipmentWebController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Shipment Web Routes
    Route::prefix('shipments')->name('shipments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ShipmentWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\ShipmentWebController::class, 'create'])->name('create');
        Route::get('/{id}', [\App\Http\Controllers\ShipmentWebController::class, 'show'])->name('show');
    });

    // Tracking Web Routes
    Route::prefix('tracking')->name('tracking.')->group(function () {
        Route::get('/', [\App\Http\Controllers\VesselWebController::class, 'index'])->name('map');
        Route::get('/vessels', [\App\Http\Controllers\VesselWebController::class, 'list'])->name('vessels');
        Route::get('/vessels/{id}', [\App\Http\Controllers\VesselWebController::class, 'show'])->name('vessel.show');
    });
});

require __DIR__.'/auth.php';
