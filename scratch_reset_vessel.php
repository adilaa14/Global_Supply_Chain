<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$malaccaStrait = [[1.25, 103.8], [1.6, 103.0], [2.2, 102.0], [3.0, 101.0], [4.5, 99.5], [6.0, 97.5], [6.0, 95.0]];
$geometry = array_merge($malaccaStrait, [[6.0, 80.0], [10.0, 75.0], [15.0, 72.0], [18.9438, 72.8358]]);

$route = \App\Models\VesselRoute::where('is_active', true)->first();
if ($route) {
    $route->update(['route_geometry' => json_encode($geometry)]);
    
    // Delete all weird history for this vessel
    \App\Models\VesselPosition::where('vessel_id', $route->vessel_id)->delete();
    
    // Insert a fresh clean start at Singapore
    \App\Models\VesselPosition::create([
        'vessel_id' => $route->vessel_id,
        'latitude' => 1.25,
        'longitude' => 103.8,
        'heading' => 300,
        'speed' => 20,
        'nav_status' => 'Under way using engine',
        'timestamp' => now(),
        'ais_provider' => 'Spire',
    ]);
    
    // Insert one slightly forward to show a small initial red line
    \App\Models\VesselPosition::create([
        'vessel_id' => $route->vessel_id,
        'latitude' => 1.30,
        'longitude' => 103.6,
        'heading' => 300,
        'speed' => 20,
        'nav_status' => 'Under way using engine',
        'timestamp' => now(),
        'ais_provider' => 'Spire',
    ]);
}
echo "Vessel reset to a clean state at Singapore with perfect geometry.\n";
