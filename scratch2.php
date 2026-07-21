<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$vessel = \App\Models\Vessel::first();
$service = app(\App\Services\TrackingService::class);
$data = $service->getLiveVesselData($vessel->id);

echo "route_geometry type: " . gettype($data['route_geometry']) . "\n";
echo "route_geometry length: " . count($data['route_geometry']) . "\n";
echo "route_geometry[0] type: " . gettype($data['route_geometry'][0]) . "\n";
echo "json_encoded: " . substr(json_encode($data['route_geometry']), 0, 100) . "...\n";
