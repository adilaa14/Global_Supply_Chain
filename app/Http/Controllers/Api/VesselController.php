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
        // Return all ports globally, removing duplicates by name
        $ports = \App\Models\Port::with('country:id,country_name')->orderBy('port_name')->get();
        $ports = $ports->unique('port_name')->values();
        
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

        $originCode = 'SGSIN';
        $originCoord = [1.264, 103.84];
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
        $destCoord = [$port->latitude, $port->longitude];

        // Use the smart Dijkstra Routing Service to avoid crossing landmasses
        $routingService = new \App\Services\RoutingService();
        $newGeometry = $routingService->findRoute($originCoord, $destCoord);


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
