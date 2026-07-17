<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Shipment;
use App\Models\VesselRoute;
use App\Models\VesselPosition;

$controller = app(\App\Http\Controllers\Api\VesselController::class);

$shipments = Shipment::with('originPort')->get();

foreach ($shipments as $shipment) {
    if (!$shipment->vessel_id || !$shipment->origin_port_id || !$shipment->destination_port_id) {
        continue;
    }
    
    // Clear old positions
    VesselPosition::where('vessel_id', $shipment->vessel_id)->delete();
    
    // Ensure route has correct origin and destination
    $route = VesselRoute::where('vessel_id', $shipment->vessel_id)->first();
    if (!$route) {
        $route = new VesselRoute(['vessel_id' => $shipment->vessel_id]);
    }
    $route->origin_port_id = $shipment->origin_port_id;
    $route->destination_port_id = $shipment->destination_port_id;
    $route->is_active = true;
    $route->save();
    
    // Redirect via controller to generate correct geometry
    $req = new \Illuminate\Http\Request([
        'port_id' => $shipment->destination_port_id,
        'origin_port_id' => $shipment->origin_port_id
    ]);
    $controller->redirectVessel($req, $shipment->vessel_id);
    
    // Explicitly place the vessel at its origin port
    if ($shipment->originPort) {
        VesselPosition::create([
            'vessel_id' => $shipment->vessel_id,
            'latitude' => $shipment->originPort->latitude,
            'longitude' => $shipment->originPort->longitude,
            'heading' => 0,
            'speed' => rand(18, 24),
            'timestamp' => now(),
            'nav_status' => 'Under way using engine'
        ]);
        echo "Aligned vessel " . $shipment->vessel->name . " to shipment " . $shipment->shipment_number . "\n";
    }
}
echo "All vessels aligned to shipment data.\n";
