<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MapService;
use App\Services\TrackingService;

class VesselController extends Controller
{
    protected MapService $mapService;
    protected TrackingService $trackingService;

    public function __construct(MapService $mapService, TrackingService $trackingService)
    {
        $this->mapService = $mapService;
        $this->trackingService = $trackingService;
    }

    public function globalMapData()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->mapService->getGlobalMapData()
        ]);
    }

    public function weatherOverlay(Request $request, \App\Services\RiskScoringEngine $riskEngine)
    {
        $countries = \App\Models\Country::all();
        $weatherData = [];

        foreach($countries as $country) {
            $weatherData[] = [
                'id' => $country->id,
                'country' => $country->country_name,
                'iso' => $country->iso_code,
                'lat' => $country->latitude,
                'lng' => $country->longitude,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $weatherData
        ]);
    }

    public function liveData(string $id)
    {
        $liveData = $this->trackingService->getLiveVesselData($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $liveData
        ]);
    }

    public function listPorts()
    {
        // Only return ports that have actual coordinates and are not the auto-generated inland mocks
        $ports = \App\Models\Port::with('country:id,country_name')->where('port_code', 'NOT LIKE', '%MCK')->get();
        return response()->json($ports);
    }

    public function redirectVessel(Request $request, string $id)
    {
        $request->validate([
            'port_id' => 'required|exists:ports,id',
        ]);

        $vessel = \App\Models\Vessel::findOrFail($id);
        $port = \App\Models\Port::with('country')->findOrFail($request->port_id);

        $activeRoute = \App\Models\VesselRoute::where('vessel_id', $vessel->id)
            ->where('is_active', true)
            ->first();

        // Precision Maritime Trunk Routes from Singapore (Avoiding ALL landmasses)
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

        // Helper function for Smart Trunk Snapping (Node-based)
        $getTrunkSnappedPath = function($coord, $isOrigin) use ($malaccaTrunk, $scsTrunk, $sundaTrunk) {
            $trunks = ['malacca' => $malaccaTrunk, 'scs' => $scsTrunk, 'sunda' => $sundaTrunk];
            $bestTrunk = $scsTrunk;
            $minDist = 999999;
            $bestPointIdx = 0;
            
            // Find the absolute closest point on ANY trunk to the coordinate
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
            
            // If origin, we go from $coord -> bestPoint -> back to Hub
            if ($isOrigin) {
                $pathToHub = array_reverse(array_slice($bestTrunk, 0, $bestPointIdx + 1));
                return array_merge([$coord], $pathToHub);
            } else {
                // If destination, we go from Hub -> bestPoint -> $coord
                $pathFromHub = array_slice($bestTrunk, 0, $bestPointIdx + 1);
                return array_merge($pathFromHub, [$coord]);
            }
        };

        $originCode = 'SGSIN';
        $originCoord = null;
        if ($activeRoute) {
            $originPort = \App\Models\Port::find($activeRoute->origin_port_id);
            if ($originPort) {
                $originCode = $originPort->port_code;
                $originCoord = [$originPort->latitude, $originPort->longitude];
            }
        }
        $destCode = $port->port_code;

        if (isset($hubRoutes[$originCode])) {
            $pathFromOriginToHub = array_reverse($hubRoutes[$originCode]);
        } else {
            $pathFromOriginToHub = $getTrunkSnappedPath($originCoord ?? [1.264, 103.84], true);
        }
        
        if (isset($hubRoutes[$destCode])) {
            $pathFromHubToDest = $hubRoutes[$destCode];
        } else {
            $pathFromHubToDest = $getTrunkSnappedPath([$port->latitude, $port->longitude], false);
        }
        
        // Combine paths (removing duplicate hub point if it exists at the connection)
        if (end($pathFromOriginToHub) === $pathFromHubToDest[0]) {
            array_pop($pathFromOriginToHub);
        }
        $newGeometry = array_merge($pathFromOriginToHub, $pathFromHubToDest);

        if ($activeRoute) {
            $activeRoute->destination_port_id = $port->id;
            $activeRoute->route_geometry = json_encode($newGeometry); 
            $activeRoute->estimated_arrival = now()->addDays(rand(5, 15));
            $activeRoute->save();
        } else {
            \App\Models\VesselRoute::create([
                'vessel_id' => $vessel->id,
                'origin_port_id' => $port->id, 
                'destination_port_id' => $port->id,
                'route_geometry' => json_encode($newGeometry),
                'estimated_arrival' => now()->addDays(rand(5, 15)),
                'is_active' => true,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Vessel successfully redirected to ' . $port->port_name . ', ' . ($port->country->country_name ?? 'Global'),
            'port' => $port
        ]);
    }
}
