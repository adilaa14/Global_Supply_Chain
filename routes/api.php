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
});
