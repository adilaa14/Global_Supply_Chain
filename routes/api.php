<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShipmentController;
use App\Http\Controllers\Api\ShipmentContainerController;
use App\Http\Controllers\Api\ShipmentDocumentController;
use App\Http\Controllers\Api\ShipmentHistoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DashboardMetricController;
use App\Http\Controllers\Api\DashboardPreferenceController;
use App\Http\Controllers\Api\DashboardWidgetController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\GlobalAlertController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // Dashboard Core API
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'summary']);
        Route::get('/summary', [DashboardController::class, 'summary']);
        Route::get('/metrics', [DashboardMetricController::class, 'index']);
        Route::get('/activity', [ActivityController::class, 'index']);
        Route::get('/widgets', [DashboardWidgetController::class, 'index']);
        Route::post('/widgets', [DashboardWidgetController::class, 'store']);
        Route::put('/widgets/order', [DashboardWidgetController::class, 'reorder']);
        Route::get('/preferences', [DashboardPreferenceController::class, 'index']);
        Route::post('/preferences', [DashboardPreferenceController::class, 'store']);
        Route::get('/alerts', [GlobalAlertController::class, 'index']);
        Route::get('/search', [DashboardController::class, 'search']);
    });

    // Shipment Management API
    Route::prefix('shipments')->group(function () {
        Route::get('/', [ShipmentController::class, 'index']);
        Route::get('/history', [ShipmentHistoryController::class, 'index']);
        Route::get('/statistics', [ShipmentController::class, 'statistics']);
        Route::get('/timeline', [ShipmentHistoryController::class, 'timeline']);
        Route::post('/', [ShipmentController::class, 'store']);
        Route::get('/{id}', [ShipmentController::class, 'show']);
        Route::put('/{id}', [ShipmentController::class, 'update']);
        Route::delete('/{id}', [ShipmentController::class, 'destroy']);
        Route::post('/documents', [ShipmentDocumentController::class, 'store']);
        Route::post('/containers', [ShipmentContainerController::class, 'store']);
    });

    // Tracking API
    Route::prefix('tracking')->group(function () {
        Route::get('/map-data', [\App\Http\Controllers\Api\VesselController::class, 'globalMapData']);
        Route::get('/vessels/{id}/live', [\App\Http\Controllers\Api\VesselController::class, 'liveData']);
    });

    // Country Intelligence API
    Route::prefix('countries')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\CountryController::class, 'index']);
        Route::get('/list', [\App\Http\Controllers\Api\CountryController::class, 'listAll']);
        Route::get('/comparison', [\App\Http\Controllers\Api\CountryComparisonController::class, 'index']);
        Route::get('/risk', [\App\Http\Controllers\Api\CountryRiskController::class, 'index']);
        Route::get('/economy', [\App\Http\Controllers\Api\CountryEconomyController::class, 'index']);
        Route::get('/trade', [\App\Http\Controllers\Api\CountryTradeController::class, 'index']);
        Route::get('/opportunity', [\App\Http\Controllers\Api\CountryController::class, 'opportunity']);
        Route::get('/{id}', [\App\Http\Controllers\Api\CountryController::class, 'show']);
    });

    // Commodity Intelligence API
    Route::prefix('commodities')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\CommodityController::class, 'index']);
        Route::get('/categories', [\App\Http\Controllers\Api\CommodityController::class, 'categories']);
        Route::get('/list', [\App\Http\Controllers\Api\CommodityController::class, 'listAll']);
        Route::get('/comparison', [\App\Http\Controllers\Api\CommodityController::class, 'comparison']);
        Route::get('/{id}/history', [\App\Http\Controllers\Api\CommodityController::class, 'history']);
        Route::get('/{id}', [\App\Http\Controllers\Api\CommodityController::class, 'show']);
    });
});

// Trade Intelligence Routes
Route::prefix('trade')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Api\TradeIntelligenceController::class, 'dashboard']);
    Route::get('opportunities', [\App\Http\Controllers\Api\TradeIntelligenceController::class, 'opportunities']);
    Route::get('risk', [\App\Http\Controllers\Api\TradeIntelligenceController::class, 'risk']);
    Route::get('forecast', [\App\Http\Controllers\Api\TradeIntelligenceController::class, 'forecast']);
    Route::get('insights', [\App\Http\Controllers\Api\TradeIntelligenceController::class, 'insights']);
    Route::get('alternative-destinations', [\App\Http\Controllers\Api\TradeIntelligenceController::class, 'alternativeDestinations']);
    Route::get('simulation', [\App\Http\Controllers\Api\TradeIntelligenceController::class, 'getSimulations']);
    Route::post('simulation', [\App\Http\Controllers\Api\TradeIntelligenceController::class, 'simulate']);
});

Route::get('/tracking/weather-overlay', [\App\Http\Controllers\Api\VesselController::class, 'weatherOverlay']);
