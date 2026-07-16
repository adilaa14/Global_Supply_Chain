<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$geometry = [
    [5.5, 98.0],
    [5.8, 80.0],
    [10.0, 75.0],
    [15.0, 72.0],
    [18.9438, 72.8358]
];

\App\Models\VesselRoute::where('is_active', true)->update([
    'route_geometry' => json_encode($geometry)
]);
echo "Updated route geometry to avoid land.\n";
