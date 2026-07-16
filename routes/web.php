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
            // We use 60 seconds cache to easily see new additions
            return \Illuminate\Support\Facades\Cache::remember('world_ports_dataset_merged', 60, function () {
                $worldPorts = [];
                try {
                    $response = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])
                        ->get('https://raw.githubusercontent.com/tayljordan/ports/master/ports.json');
                    if ($response->successful()) {
                        $worldPorts = $response->json()['ports'] ?? [];
                    }
                } catch (\Exception $e) {}

                // Get local ports from DB
                $localPorts = \App\Models\Port::with('country')->get()->map(function($port) {
                    return [
                        'point_of_interest' => $port->port_name,
                        'country' => $port->country ? $port->country->country_name : 'Unknown',
                        'latitude' => (float) $port->latitude,
                        'longitude' => (float) $port->longitude,
                        'port_size' => 'Custom (Database)',
                        'state' => $port->port_code
                    ];
                })->toArray();

                return array_merge($localPorts, $worldPorts);
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
        Route::get('/countries/watchlist', [\App\Http\Controllers\CountryIntelligenceWebController::class, 'watchlist'])->name('countries.watchlist');
        Route::get('/countries/{id}', [\App\Http\Controllers\CountryIntelligenceWebController::class, 'show'])->name('countries.show');

        Route::get('/commodities', [\App\Http\Controllers\CommodityIntelligenceWebController::class, 'index'])->name('commodities.index');
        Route::get('/commodities/compare', [\App\Http\Controllers\CommodityIntelligenceWebController::class, 'compare'])->name('commodities.compare');
        Route::get('/commodities/{id}', [\App\Http\Controllers\CommodityIntelligenceWebController::class, 'show'])->name('commodities.show');
        
        Route::get('/news', function() { return \Inertia\Inertia::render('Intelligence/News'); })->name('news');
    });

    // Analytics Web Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/visualization', function() { return \Inertia\Inertia::render('Analytics/Visualization'); })->name('visualization');
        Route::get('/currency-impact', function() { return \Inertia\Inertia::render('Analytics/CurrencyImpact'); })->name('currency-impact');
    });
});

require __DIR__.'/auth.php';



// Fallback for removed Trade Intelligence routes to prevent 404 on refresh
Route::get('/trade/{any?}', function () {
    return redirect('/dashboard');
})->where('any', '.*');


// Admin Dashboard Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return \Inertia\Inertia::render('Admin/Dashboard', [
            'users' => App\Models\User::count(),
            'ports' => App\Models\Port::count(),
            'articles' => App\Models\News::count(),
        ]);
    })->name('dashboard');

    Route::get('/users', function () {
        return \Inertia\Inertia::render('Admin/Users', [
            'users' => App\Models\User::with('company')->latest()->paginate(50)
        ]);
    })->name('users');

    Route::get('/ports', function () {
        return \Inertia\Inertia::render('Admin/Ports', [
            'ports' => App\Models\Port::with('country')->latest()->paginate(50),
            'countries' => App\Models\Country::orderBy('country_name')->get(['id', 'country_name', 'iso_alpha2'])
        ]);
    })->name('ports');

    Route::post('/ports', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'port_code' => 'required|string|max:10',
            'port_name' => 'required|string|max:255',
            'country_id' => 'required|uuid|exists:countries,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|string',
        ]);
        App\Models\Port::create($validated);
        return redirect()->back()->with('success', 'Port created successfully.');
    });

    Route::put('/ports/{id}', function (\Illuminate\Http\Request $request, $id) {
        $port = App\Models\Port::findOrFail($id);
        $port->update($request->only(['port_code', 'port_name', 'country_id', 'latitude', 'longitude', 'status']));
        return redirect()->back()->with('success', 'Port updated successfully.');
    });

    Route::delete('/ports/{id}', function ($id) {
        App\Models\Port::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Port deleted successfully.');
    });

    Route::get('/articles', function () {
        return \Inertia\Inertia::render('Admin/Articles', [
            'articles' => App\Models\News::with('country')->latest()->paginate(50),
            'countries' => App\Models\Country::orderBy('country_name')->get()
        ]);
    })->name('articles');

    Route::post('/articles', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'category' => 'required|in:Logistics,Trade,Shipping,Economy',
            'summary' => 'required|string',
            'sentiment' => 'required|in:Positive,Neutral,Negative',
            'published_at' => 'required|date',
            'country_id' => 'required|exists:countries,id'
        ]);
        App\Models\News::create($validated);
        return redirect()->back()->with('success', 'Article created successfully.');
    });

    Route::put('/articles/{id}', function (\Illuminate\Http\Request $request, $id) {
        $article = App\Models\News::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'category' => 'required|in:Logistics,Trade,Shipping,Economy',
            'summary' => 'required|string',
            'sentiment' => 'required|in:Positive,Neutral,Negative',
            'published_at' => 'required|date',
            'country_id' => 'required|exists:countries,id'
        ]);
        $article->update($validated);
        return redirect()->back()->with('success', 'Article updated successfully.');
    });

    Route::delete('/articles/{id}', function ($id) {
        App\Models\News::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Article deleted successfully.');
    });
});
