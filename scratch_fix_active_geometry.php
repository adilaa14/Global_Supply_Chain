<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$malaccaStrait = [[1.264, 103.84], [1.15, 103.4], [2.0, 102.0], [3.0, 100.5], [4.0, 99.5], [5.5, 98.0]];
$geometry = array_merge($malaccaStrait, [[5.8, 80.0], [10.0, 75.0], [15.0, 72.0], [18.9438, 72.8358]]);

\App\Models\VesselRoute::where('is_active', true)->update([
    'route_geometry' => json_encode($geometry)
]);
echo "Active route geometry updated to include Malacca Strait waypoints.\n";
