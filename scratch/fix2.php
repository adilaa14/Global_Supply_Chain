<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vessel;
use App\Models\VesselRoute;
use App\Models\VesselPosition;
use App\Models\Port;

$controller = app(\App\Http\Controllers\Api\VesselController::class);

$jakarta = Port::where('port_code', 'IDJKT')->first();
$manila = Port::where('port_code', 'PHMNL')->first();
$melbourne = Port::where('port_code', 'AUMEL')->first();

// Reset HMM ALGECIRAS
$hmm = Vessel::where('name', 'HMM ALGECIRAS')->first();
if ($hmm) {
    VesselPosition::where('vessel_id', $hmm->id)->delete();
    
    $route = VesselRoute::where('vessel_id', $hmm->id)->first();
    if ($route) {
        $route->origin_port_id = $jakarta->id;
        $route->destination_port_id = $manila->id;
        $route->save();
        
        $req = new \Illuminate\Http\Request([
            'port_id' => $manila->id,
            'origin_port_id' => $jakarta->id
        ]);
        $controller->redirectVessel($req, $hmm->id);
    }
    
    // Set initial position to Jakarta
    VesselPosition::create([
        'vessel_id' => $hmm->id,
        'latitude' => $jakarta->latitude,
        'longitude' => $jakarta->longitude,
        'heading' => 0,
        'speed' => 23,
        'timestamp' => now(),
        'nav_status' => 'Under way using engine'
    ]);
    echo "Fixed HMM ALGECIRAS\n";
}

// Reset CMA CGM ANTOINE
$cma = Vessel::where('name', 'CMA CGM ANTOINE')->first();
if ($cma) {
    VesselPosition::where('vessel_id', $cma->id)->delete();
    
    $route = VesselRoute::where('vessel_id', $cma->id)->first();
    if ($route) {
        $route->origin_port_id = $jakarta->id;
        $route->destination_port_id = $melbourne->id;
        $route->save();
        
        $req = new \Illuminate\Http\Request([
            'port_id' => $melbourne->id,
            'origin_port_id' => $jakarta->id
        ]);
        $controller->redirectVessel($req, $cma->id);
    }
    
    // Set initial position to Jakarta
    VesselPosition::create([
        'vessel_id' => $cma->id,
        'latitude' => $jakarta->latitude,
        'longitude' => $jakarta->longitude,
        'heading' => 0,
        'speed' => 21,
        'timestamp' => now(),
        'nav_status' => 'Under way using engine'
    ]);
    echo "Fixed CMA CGM ANTOINE\n";
}
