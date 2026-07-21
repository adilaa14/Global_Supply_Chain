<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$vessel = \App\Models\Vessel::first();
if ($vessel) {
    $service = app(\App\Services\TrackingService::class);
    $data = $service->getLiveVesselData($vessel->id);
    echo "Type of route_geometry: " . gettype($data['route_geometry'] ?? null) . "\n";
    if (isset($data['route_geometry'])) {
        echo "First item: " . json_encode($data['route_geometry'][0]) . "\n";
    }
} else {
    echo "No vessel found\n";
}
