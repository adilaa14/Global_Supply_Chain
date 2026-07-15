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
        Route::get('/ports', function() { return \Inertia\Inertia::render('Tracking/PortsMap'); })->name('ports');
        Route::get('/api/world-ports', function() {
            return \Illuminate\Support\Facades\Cache::remember('world_ports_dataset', 86400, function () {
                $response = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])
                    ->get('https://raw.githubusercontent.com/tayljordan/ports/master/ports.json');
                if ($response->successful()) {
                    return $response->json()['ports'] ?? [];
                }
                return [];
            });
        })->name('api.world-ports');
        
        Route::get('/api/worldbank/{iso}/{indicator}', function($iso, $indicator) {
            $cacheKey = "wb_{$iso}_{$indicator}";
            $cached = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if ($cached) return $cached;

            try {
                $response = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])
                    ->timeout(5)
                    ->get("https://api.worldbank.org/v2/country/{$iso}/indicator/{$indicator}?format=json&per_page=6");
                
                if ($response->successful()) {
                    $data = $response->json();
                    \Illuminate\Support\Facades\Cache::put($cacheKey, $data, 3600);
                    return $data;
                }
            } catch (\Exception $e) {
                // Return empty if fails, but DO NOT cache it
            }
            return [];
        })->name('api.worldbank');

        Route::get('/api/exchange-rates/{base}/{symbols}/{startDate}/{endDate}', function($base, $symbols, $startDate, $endDate) {
            $cacheKey = "er_{$base}_{$symbols}_{$startDate}_{$endDate}";
            $cached = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if ($cached) return $cached;

            try {
                $response = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])
                    ->timeout(8)
                    ->get("https://api.frankfurter.app/{$startDate}..{$endDate}?from={$base}&to={$symbols}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    \Illuminate\Support\Facades\Cache::put($cacheKey, $data, 3600);
                    return $data;
                }
            } catch (\Exception $e) {
                // Return empty if fails, but DO NOT cache it
            }
            return [];
        })->name('api.exchange-rates');
        Route::get('/vessels', [\App\Http\Controllers\VesselWebController::class, 'list'])->name('vessels');
        Route::get('/vessels/{id}', [\App\Http\Controllers\VesselWebController::class, 'show'])->name('vessel.show');
    });

    // Country Intelligence Web Routes
    Route::prefix('intelligence')->name('intelligence.')->group(function () {
        Route::get('/countries', [\App\Http\Controllers\CountryIntelligenceWebController::class, 'index'])->name('countries.index');
        Route::get('/countries/compare', [\App\Http\Controllers\CountryIntelligenceWebController::class, 'compare'])->name('countries.compare');
        Route::get('/countries/{id}', [\App\Http\Controllers\CountryIntelligenceWebController::class, 'show'])->name('countries.show');

        Route::get('/commodities', [\App\Http\Controllers\CommodityIntelligenceWebController::class, 'index'])->name('commodities.index');
        Route::get('/commodities/compare', [\App\Http\Controllers\CommodityIntelligenceWebController::class, 'compare'])->name('commodities.compare');
        Route::get('/commodities/{id}', [\App\Http\Controllers\CommodityIntelligenceWebController::class, 'show'])->name('commodities.show');
    });

    // Analytics Web Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/visualization', function() { return \Inertia\Inertia::render('Analytics/Visualization'); })->name('visualization');
        Route::get('/currency-impact', function() { return \Inertia\Inertia::render('Analytics/CurrencyImpact'); })->name('currency-impact');
    });
});

require __DIR__.'/auth.php';

// Trade Intelligence Routes
Route::middleware(['auth', 'verified'])->prefix('trade')->name('trade.')->group(function () {
    Route::get('/', function () { return \Inertia\Inertia::render('TradeIntelligence/Index'); })->name('index');
    Route::get('/opportunity', function () { return \Inertia\Inertia::render('TradeIntelligence/Opportunity'); })->name('opportunity');
    Route::get('/simulation', function () { return \Inertia\Inertia::render('TradeIntelligence/Simulation'); })->name('simulation');
    Route::get('/market', function () { return \Inertia\Inertia::render('TradeIntelligence/Market'); })->name('market');
    Route::get('/risk', function () { return \Inertia\Inertia::render('TradeIntelligence/Risk'); })->name('risk');
    Route::get('/alternative-destination', function () { return \Inertia\Inertia::render('TradeIntelligence/AlternativeDestination'); })->name('alternative_destination');
    Route::get('/forecast', function () { return \Inertia\Inertia::render('TradeIntelligence/Forecast'); })->name('forecast');
    Route::get('/insights', function () { return \Inertia\Inertia::render('TradeIntelligence/Insights'); })->name('insights');
});
