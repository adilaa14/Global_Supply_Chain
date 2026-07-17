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

    public function listVessels()
    {
        $vessels = \App\Models\Vessel::where('status', 'Active')
            ->whereDoesntHave('shipments', function ($query) {
                $query->whereIn('status', ['Preparing', 'In Transit']);
            })
            ->get();
            
        return response()->json($vessels);
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
        // Safely navigate Singapore Strait east, then down Karimata Strait, to Sunda Strait
        $sundaTrunk = [[1.264, 103.84], [1.2, 104.2], [0.0, 105.5], [-2.0, 106.0], [-4.0, 106.0], [-5.5, 105.8], [-6.0, 105.3], [-6.5, 105.0], [-10.0, 104.0]];

        $hubRoutes = [
            'INBOM' => array_merge($malaccaTrunk, [[6.0, 80.0], [10.0, 75.0], [15.0, 72.0], [18.9438, 72.8358]]),
            'INMAA' => array_merge($malaccaTrunk, [[10.0, 85.0], [13.0827, 80.2707]]),
            'NLRTM' => array_merge($malaccaTrunk, [[6.0, 80.0], [12, 60], [12.5, 43.5], [20, 38], [28, 33.5], [31.5, 32], [35, 15], [37.5, 4], [36.5, -6.5], [43, -10], [49.5, -4], [51.885, 4.2867]]),
            'AEJEA' => array_merge($malaccaTrunk, [[6.0, 80.0], [15, 65], [24, 60], [25.0112, 55.0556]]),
            'CNSHA' => array_merge($scsTrunk, [[25.0, 122.0], [28.0, 123.0], [31.2222, 121.4581]]),
            'USLAX' => array_merge($scsTrunk, [[25.0, 130.0], [30.0, 150.0], [35.0, 180.0], [35.0, 210.0], [34.0, 230.0], [33.7288, 241.738]]),
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
        $originPortId = null;

        if ($request->has('origin_port_id')) {
            $originPortId = $request->origin_port_id;
        } elseif ($activeRoute) {
            // Keep the original origin port when redirecting mid-voyage
            $originPortId = $activeRoute->origin_port_id;
        }

        if ($originPortId) {
            $originPort = \App\Models\Port::find($originPortId);
            if ($originPort) {
                $originCode = $originPort->port_code;
                $originCoord = [$originPort->latitude, $originPort->longitude];
            }
        }
        $destCode = $port->port_code;

        // Add missing major ports to HubRoutes to avoid straight-line land cuts
        $javaSeaTrunk = [[1.264, 103.84], [1.2, 104.2], [0.0, 106.0], [-3.0, 107.0], [-5.0, 106.5]];
        $hubRoutes['IDJKT'] = array_merge($javaSeaTrunk, [[-5.9, 106.8], [-6.1, 106.88]]);
        $suezToGibraltar = [[6.0, 80.0], [12, 60], [12.5, 43.5], [20, 38], [28, 33.5], [31.5, 32], [35, 15], [36.5, -6.5]];
        
        $hubRoutes = [
            'INBOM' => array_merge($malaccaTrunk, [[6.0, 80.0], [10.0, 75.0], [15.0, 72.0], [18.9438, 72.8358]]),
            'INMAA' => array_merge($malaccaTrunk, [[10.0, 85.0], [13.0827, 80.2707]]),
            'LKMBA' => array_merge($malaccaTrunk, [[6.0, 80.0], [6.9, 79.8]]),
            'PKKHI' => array_merge($malaccaTrunk, [[6.0, 80.0], [10.0, 70.0], [20.0, 65.0], [24.8, 66.9]]),
            'OMSAA' => array_merge($malaccaTrunk, [[6.0, 80.0], [12.0, 60.0], [16.9, 54.0]]),
            'AEJEA' => array_merge($malaccaTrunk, [[6.0, 80.0], [15, 65], [24, 60], [25.0112, 55.0556]]),
            'SAJED' => array_merge($malaccaTrunk, [[6.0, 80.0], [12, 60], [12.5, 43.5], [20, 38], [21.4, 39.1]]),
            'EGPSD' => array_merge($malaccaTrunk, [[6.0, 80.0], [12, 60], [12.5, 43.5], [20, 38], [28, 33.5], [31.2, 32.3]]),
            
            // Europe / Mediterranean
            'NLRTM' => array_merge($malaccaTrunk, $suezToGibraltar, [[43, -10], [49.5, -4], [51.885, 4.2867]]),
            'GBFEL' => array_merge($malaccaTrunk, $suezToGibraltar, [[43, -10], [49.5, -4], [51.96, 1.32]]),
            'DEHAM' => array_merge($malaccaTrunk, $suezToGibraltar, [[43, -10], [49.5, -4], [51.9, 3.0], [53.54, 9.99]]),
            'BEANR' => array_merge($malaccaTrunk, $suezToGibraltar, [[43, -10], [49.5, -4], [51.2, 4.4]]),
            'ITGOA' => array_merge($malaccaTrunk, [[6.0, 80.0], [12, 60], [12.5, 43.5], [20, 38], [28, 33.5], [31.5, 32], [35, 15], [38, 10], [44.4, 8.9]]),
            'ESBCN' => array_merge($malaccaTrunk, [[6.0, 80.0], [12, 60], [12.5, 43.5], [20, 38], [28, 33.5], [31.5, 32], [35, 15], [41.3, 2.1]]),
            'FRMRS' => array_merge($malaccaTrunk, [[6.0, 80.0], [12, 60], [12.5, 43.5], [20, 38], [28, 33.5], [31.5, 32], [35, 15], [38, 10], [43.3, 5.3]]),
            
            // East Coast Americas
            'USNYC' => array_merge($malaccaTrunk, $suezToGibraltar, [[38, -30], [40, -60], [40.67, -74.04]]),
            'USMIA' => array_merge($malaccaTrunk, $suezToGibraltar, [[38, -30], [25, -60], [25.76, -80.19]]),
            'ARBUE' => array_merge($sundaTrunk, [[-20.0, 90.0], [-25.0, 75.0], [-30.0, 55.0], [-35.0, 30.0], [-33.9, 18.433], [-34, -40], [-34.6, -58.3]]),
            'BRSSZ' => array_merge($sundaTrunk, [[-20.0, 90.0], [-25.0, 75.0], [-30.0, 55.0], [-35.0, 30.0], [-33.9, 18.433], [-25, -20], [-23.9, -46.3]]),
            
            // East Asia / Pacific
            'CNSHA' => array_merge($scsTrunk, [[25.0, 122.0], [28.0, 123.0], [31.2222, 121.4581]]),
            'JPTYO' => array_merge($scsTrunk, [[25.0, 122.0], [30.0, 130.0], [33.0, 135.0], [35.0, 140.0], [35.61, 139.79]]),
            'KRPUS' => array_merge($scsTrunk, [[25.0, 122.0], [30.0, 128.0], [35.1, 129.0]]),
            'TWTPE' => array_merge($scsTrunk, [[25.0, 121.0], [25.1, 121.3]]),
            'HKHKG' => array_merge($scsTrunk, [[21.5, 114.0], [22.33, 114.13]]),
            'PHMNL' => array_merge(array_slice($scsTrunk, 0, 5), [[14.0, 118.0], [14.6, 120.9]]), // Branch off at [15.0, 113.0]
            'VNSGN' => array_merge(array_slice($scsTrunk, 0, 4), [[10.0, 107.0], [10.7, 106.7]]), // Branch off at [10.0, 110.0]
            'THLCH' => array_merge(array_slice($scsTrunk, 0, 3), [[10.0, 101.0], [13.0, 100.8]]), // Branch off at [5.0, 107.0]
            
            // West Coast Americas (Across Pacific)
            'USLAX' => array_merge($scsTrunk, [[25.0, 130.0], [30.0, 150.0], [35.0, 180.0], [35.0, 210.0], [34.0, 230.0], [33.72, -118.26]]),
            'USSEA' => array_merge($scsTrunk, [[25.0, 130.0], [30.0, 150.0], [45.0, 180.0], [47.0, 210.0], [47.6, -122.3]]),
            'CAVAN' => array_merge($scsTrunk, [[25.0, 130.0], [30.0, 150.0], [45.0, 180.0], [49.0, 210.0], [49.2, -123.1]]),
            'MXZLO' => array_merge($scsTrunk, [[25.0, 130.0], [30.0, 150.0], [20.0, 180.0], [19.0, -104.3]]),
            'PECLO' => array_merge($scsTrunk, [[25.0, 130.0], [30.0, 150.0], [10.0, 180.0], [0.0, -100], [-12.0, -77.1]]),
            'CLVAP' => array_merge($scsTrunk, [[25.0, 130.0], [30.0, 150.0], [0.0, 180.0], [-15.0, -100], [-33.0, -71.6]]),
            
            // Africa & Oceania
            'ZACPT' => array_merge($sundaTrunk, [[-20.0, 90.0], [-25.0, 75.0], [-30.0, 55.0], [-35.0, 30.0], [-33.9, 18.433]]),
            'KEMBA' => array_merge($malaccaTrunk, [[6.0, 80.0], [-2.0, 60.0], [-4.0, 39.6]]),
            'TZDAR' => array_merge($malaccaTrunk, [[6.0, 80.0], [-4.0, 60.0], [-6.8, 39.2]]),
            'AUSYD' => array_merge($sundaTrunk, [[-15.0, 105.0], [-25.0, 110.0], [-35.0, 115.0], [-40.0, 130.0], [-40.0, 145.0], [-37.0, 150.0], [-33.8, 151.2]]),
            'AUMEL' => array_merge($sundaTrunk, [[-15.0, 105.0], [-25.0, 110.0], [-35.0, 115.0], [-40.0, 130.0], [-40.0, 140.0], [-37.8, 144.9]]),
            'NZAKL' => array_merge($sundaTrunk, [[-15.0, 105.0], [-25.0, 110.0], [-35.0, 115.0], [-40.0, 130.0], [-40.0, 150.0], [-36.8, 174.7]]),
            
            // Local
            'IDJKT' => array_merge($javaSeaTrunk, [[-5.9, 106.8], [-6.1, 106.88]]),
            'IDTPE' => array_merge($javaSeaTrunk, [[-6.0, 109.0], [-7.2, 112.7]]),
            'BNDCH' => array_merge($malaccaTrunk, [[5.5, 95.3]]),
            'BGCGP' => array_merge($malaccaTrunk, [[10.0, 90.0], [22.3, 91.8]]),
            'MYPKG' => [[1.264, 103.84], [1.6, 103.0], [2.2, 102.0], [3.0, 101.2]],
            'SGSIN' => [[1.264, 103.84]]
        ];

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
        
        // Smart Trunk Optimization: Prevent going back to Hub if they share trunk path
        $intersectionIdxOrigin = -1;
        $intersectionIdxDest = -1;
        
        foreach ($pathFromOriginToHub as $i => $ptOrigin) {
            foreach ($pathFromHubToDest as $j => $ptDest) {
                if (json_encode($ptOrigin) === json_encode($ptDest)) {
                    $intersectionIdxOrigin = $i;
                    $intersectionIdxDest = $j;
                    break 2;
                }
            }
        }

        if ($intersectionIdxOrigin !== -1 && $intersectionIdxDest !== -1) {
            $path1 = array_slice($pathFromOriginToHub, 0, $intersectionIdxOrigin);
            $path2 = array_slice($pathFromHubToDest, $intersectionIdxDest);
            $newGeometry = array_merge($path1, $path2);
        } else {
            if (end($pathFromOriginToHub) === $pathFromHubToDest[0]) {
                array_pop($pathFromOriginToHub);
            }
            $newGeometry = array_merge($pathFromOriginToHub, $pathFromHubToDest);
        }

        // --- DIRECT ROUTE OVERRIDES ---
        // 1. Indonesia to East Asia (South China Sea bypass)
        $eastAsiaPorts = ['JPTYO', 'CNSHA', 'HKHKG', 'TWTPE', 'PHMNL', 'KRPUS'];
        if (in_array($originCode, ['IDJKT', 'IDTPE']) && in_array($destCode, $eastAsiaPorts)) {
            $directLink = [[-6.1, 106.88], [-5.9, 106.8], [-5.0, 106.5], [-3.0, 107.0], [0.0, 107.0], [2.0, 107.0]];
            $destPath = $hubRoutes[$destCode] ?? $scsTrunk;
            $scsIntersectionIdx = -1;
            foreach ($destPath as $idx => $pt) {
                if (json_encode($pt) === json_encode([5.0, 107.0])) {
                    $scsIntersectionIdx = $idx;
                    break;
                }
            }
            if ($scsIntersectionIdx !== -1) {
                $newGeometry = array_merge($directLink, array_slice($destPath, $scsIntersectionIdx));
            }
        }
        
        // 2. Africa to Americas (Direct Atlantic Crossing to avoid routing all the way back to Singapore)
        $americas = ['USNYC', 'USMIA', 'ARBUE', 'BRSSZ'];
        if ($originCode === 'ZACPT' && in_array($destCode, $americas)) {
            $destPortsCoords = [
                'USNYC' => [[-20, 0], [0, -30], [20, -50], [40.67, -74.04]],
                'USMIA' => [[-20, 0], [0, -30], [20, -60], [25.76, -80.19]],
                'ARBUE' => [[-34, -10], [-34, -40], [-34.6, -58.3]],
                'BRSSZ' => [[-28, -10], [-25, -20], [-23.9, -46.3]],
            ];
            $newGeometry = array_merge([[-33.9, 18.433]], $destPortsCoords[$destCode]);
        } elseif (in_array($originCode, $americas) && $destCode === 'ZACPT') {
            $originPortsCoords = [
                'USNYC' => [[40.67, -74.04], [20, -50], [0, -30], [-20, 0]],
                'USMIA' => [[25.76, -80.19], [20, -60], [0, -30], [-20, 0]],
                'ARBUE' => [[-34.6, -58.3], [-34, -40], [-34, -10]],
                'BRSSZ' => [[-23.9, -46.3], [-25, -20], [-28, -10]],
            ];
            $newGeometry = array_merge($originPortsCoords[$originCode], [[-33.9, 18.433]]);
        }

        if ($activeRoute) {
            $activeRoute->destination_port_id = $port->id;
            if ($originPortId) {
                $activeRoute->origin_port_id = $originPortId;
            }
            $activeRoute->route_geometry = json_encode($newGeometry); 
            $activeRoute->estimated_arrival = now()->addDays(rand(5, 15));
            $activeRoute->save();
        } else {
            \App\Models\VesselRoute::create([
                'vessel_id' => $vessel->id,
                'origin_port_id' => $originPortId ?? $port->id, 
                'destination_port_id' => $port->id,
                'route_geometry' => json_encode($newGeometry),
                'estimated_arrival' => now()->addDays(rand(5, 15)),
                'is_active' => true,
            ]);
        }
        
        // Clear old vessel positions so the ship starts fresh at the new origin instead of being stuck at the old destination
        \App\Models\VesselPosition::where('vessel_id', $vessel->id)->delete();
        if ($originCoord) {
            \App\Models\VesselPosition::create([
                'vessel_id' => $vessel->id,
                'latitude' => $originCoord[0],
                'longitude' => $originCoord[1],
                'heading' => 0,
                'speed' => rand(15, 24),
                'timestamp' => now(),
                'nav_status' => 'Under way using engine'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Vessel successfully redirected to ' . $port->port_name . ', ' . ($port->country->country_name ?? 'Global'),
            'port' => $port
        ]);
    }
}
