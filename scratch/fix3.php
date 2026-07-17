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

$ports = [
    'SGSIN' => Port::where('port_code', 'SGSIN')->first(),
    'NLRTM' => Port::where('port_code', 'NLRTM')->first(),
    'CNSHA' => Port::where('port_code', 'CNSHA')->first(),
    'USLAX' => Port::where('port_code', 'USLAX')->first(),
];

// 1. Fix EVER GIVEN (Shanghai to LA)
$everGiven = Vessel::where('name', 'EVER GIVEN')->first();
if ($everGiven) {
    VesselPosition::where('vessel_id', $everGiven->id)->delete();
    
    $route = VesselRoute::where('vessel_id', $everGiven->id)->first();
    if (!$route) {
        $route = new VesselRoute(['vessel_id' => $everGiven->id]);
    }
    
    $route->origin_port_id = $ports['CNSHA']->id;
    $route->destination_port_id = $ports['USLAX']->id;
    $route->is_active = true;
    $route->save();
    
    $req = new \Illuminate\Http\Request([
        'port_id' => $ports['USLAX']->id,
        'origin_port_id' => $ports['CNSHA']->id
    ]);
    $controller->redirectVessel($req, $everGiven->id);
    
    // Set initial position to Shanghai
    VesselPosition::create([
        'vessel_id' => $everGiven->id,
        'latitude' => $ports['CNSHA']->latitude,
        'longitude' => $ports['CNSHA']->longitude,
        'heading' => 0,
        'speed' => 20,
        'timestamp' => now(),
        'nav_status' => 'Under way using engine'
    ]);
    echo "Fixed EVER GIVEN\n";
}

// 2. Fix MSC GULSUN (Singapore to Rotterdam)
$msc = Vessel::where('name', 'MSC GULSUN')->first();
if ($msc) {
    VesselPosition::where('vessel_id', $msc->id)->delete();
    
    $route = VesselRoute::where('vessel_id', $msc->id)->first();
    if (!$route) {
        $route = new VesselRoute(['vessel_id' => $msc->id]);
    }
    
    $route->origin_port_id = $ports['SGSIN']->id;
    $route->destination_port_id = $ports['NLRTM']->id;
    $route->is_active = true;
    $route->save();
    
    $req = new \Illuminate\Http\Request([
        'port_id' => $ports['NLRTM']->id,
        'origin_port_id' => $ports['SGSIN']->id
    ]);
    $controller->redirectVessel($req, $msc->id);
    
    // Set initial position to Singapore
    VesselPosition::create([
        'vessel_id' => $msc->id,
        'latitude' => $ports['SGSIN']->latitude,
        'longitude' => $ports['SGSIN']->longitude,
        'heading' => 0,
        'speed' => 22,
        'timestamp' => now(),
        'nav_status' => 'Under way using engine'
    ]);
    echo "Fixed MSC GULSUN\n";
}
