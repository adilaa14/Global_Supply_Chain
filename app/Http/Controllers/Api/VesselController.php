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

    public function liveData(string $id)
    {
        $liveData = $this->trackingService->getLiveVesselData($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $liveData
        ]);
    }
}
