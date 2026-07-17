<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Recalculate routes for all active shipments to fix broken geometries
$vessels = \App\Models\Vessel::all();
$controller = app(\App\Http\Controllers\Api\VesselController::class);

foreach ($vessels as $vessel) {
    $route = \App\Models\VesselRoute::where('vessel_id', $vessel->id)->where('is_active', true)->first();
    if ($route) {
        $req = new \Illuminate\Http\Request([
            'port_id' => $route->destination_port_id,
            'origin_port_id' => $route->origin_port_id
        ]);
        $controller->redirectVessel($req, $vessel->id);
        echo "Updated route for " . $vessel->name . "\n";
    }
}
echo "All routes recalculated.\n";
