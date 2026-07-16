<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$malaccaTrunk = [[1.264, 103.84], [1.6, 103.0], [2.2, 102.0], [3.0, 101.0], [4.5, 99.5], [6.0, 97.5], [6.0, 95.0]];
$scsTrunk = [[1.264, 103.84], [1.3, 104.2], [5.0, 107.0], [10.0, 110.0], [15.0, 113.0], [20.0, 120.0]];
$sundaTrunk = [[1.264, 103.84], [1.0, 104.5], [0.0, 105.0], [-3.0, 107.0], [-4.0, 107.0], [-6.0, 105.5], [-7.0, 105.0], [-10.0, 104.0]];

$hubRoutes = [
    'INBOM' => array_merge($malaccaTrunk, [[6.0, 80.0], [10.0, 75.0], [15.0, 72.0], [18.9438, 72.8358]]),
    'INMAA' => array_merge($malaccaTrunk, [[10.0, 85.0], [13.0827, 80.2707]]),
    'NLRTM' => array_merge($malaccaTrunk, [[6.0, 80.0], [12, 60], [12.5, 43.5], [20, 38], [28, 33.5], [31.5, 32], [35, 15], [37.5, 4], [36.5, -6.5], [43, -10], [49.5, -4], [51.885, 4.2867]]),
    'AEJEA' => array_merge($malaccaTrunk, [[6.0, 80.0], [15, 65], [24, 60], [25.0112, 55.0556]]),
    'CNSHA' => array_merge($scsTrunk, [[25.0, 122.0], [28.0, 123.0], [31.2222, 121.4581]]),
    'USLAX' => array_merge($scsTrunk, [[25.0, 130.0], [30.0, 150.0], [35.0, 180.0], [35.0, -150.0], [34.0, -130.0], [33.7288, -118.262]]),
    'ZACPT' => array_merge($sundaTrunk, [[-20.0, 90.0], [-25.0, 75.0], [-30.0, 55.0], [-35.0, 30.0], [-33.9, 18.433]]),
    'AUSYD' => array_merge($sundaTrunk, [[-15.0, 105.0], [-25.0, 110.0], [-35.0, 115.0], [-40.0, 130.0], [-40.0, 145.0], [-37.0, 150.0], [-33.8688, 151.2093]]),
    'SGSIN' => [[1.264, 103.84]]
];

$getTrunkSnappedPath = function($coord, $isOrigin) use ($malaccaTrunk, $scsTrunk, $sundaTrunk) {
    $trunks = ['malacca' => $malaccaTrunk, 'scs' => $scsTrunk, 'sunda' => $sundaTrunk];
    $bestTrunk = $scsTrunk;
    $minDist = 999999;
    $bestPointIdx = 0;
    
    foreach ($trunks as $trunkName => $trunk) {
        foreach ($trunk as $idx => $wp) {
            $dist = sqrt(pow($wp[0] - $coord[0], 2) + pow($wp[1] - $coord[1], 2));
            if ($dist < $minDist) { 
                $minDist = $dist; 
                $bestTrunk = $trunk;
                $bestPointIdx = $idx;
            }
        }
    }
    
    if ($isOrigin) {
        $pathToHub = array_reverse(array_slice($bestTrunk, 0, $bestPointIdx + 1));
        return array_merge([$coord], $pathToHub);
    } else {
        $pathFromHub = array_slice($bestTrunk, 0, $bestPointIdx + 1);
        return array_merge($pathFromHub, [$coord]);
    }
};

$activeRoutes = \App\Models\VesselRoute::where('is_active', true)->get();
foreach ($activeRoutes as $route) {
    $originCode = 'SGSIN';
    $originCoord = null;
    $originPort = \App\Models\Port::find($route->origin_port_id);
    if ($originPort) {
        $originCode = $originPort->port_code;
        $originCoord = [$originPort->latitude, $originPort->longitude];
    }
    
    $destPort = \App\Models\Port::find($route->destination_port_id);
    if (!$destPort) continue;
    $destCode = $destPort->port_code;

    if (isset($hubRoutes[$originCode])) {
        $pathFromOriginToHub = array_reverse($hubRoutes[$originCode]);
    } else {
        $pathFromOriginToHub = $getTrunkSnappedPath($originCoord ?? [1.264, 103.84], true);
    }
    
    if (isset($hubRoutes[$destCode])) {
        $pathFromHubToDest = $hubRoutes[$destCode];
    } else {
        $pathFromHubToDest = $getTrunkSnappedPath([$destPort->latitude, $destPort->longitude], false);
    }
    
    if (end($pathFromOriginToHub) === $pathFromHubToDest[0]) {
        array_pop($pathFromOriginToHub);
    }
    $newGeometry = array_merge($pathFromOriginToHub, $pathFromHubToDest);

    $route->update(['route_geometry' => json_encode($newGeometry)]);
    
    // Clear history
    \App\Models\VesselPosition::where('vessel_id', $route->vessel_id)->delete();
    
    // Reset to origin
    if ($originPort) {
        \App\Models\VesselPosition::create([
            'vessel_id' => $route->vessel_id,
            'latitude' => $originPort->latitude,
            'longitude' => $originPort->longitude,
            'heading' => 0,
            'speed' => 20,
            'nav_status' => 'Under way using engine',
            'timestamp' => now(),
            'ais_provider' => 'Spire',
        ]);
        
        if (count($newGeometry) > 1) {
            $secondWp = $newGeometry[1];
            $lat = $originPort->latitude + ($secondWp[0] - $originPort->latitude) * 0.1;
            $lng = $originPort->longitude + ($secondWp[1] - $originPort->longitude) * 0.1;
            
            \App\Models\VesselPosition::create([
                'vessel_id' => $route->vessel_id,
                'latitude' => $lat,
                'longitude' => $lng,
                'heading' => 0,
                'speed' => 20,
                'nav_status' => 'Under way using engine',
                'timestamp' => now(),
                'ais_provider' => 'Spire',
            ]);
        }
    }
}

echo "All routes updated with Node-Based Trunk Snapping.\n";
