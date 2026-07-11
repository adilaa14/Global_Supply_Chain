<?php

namespace App\Services;

use App\Repositories\VesselRepository;
use App\Repositories\TrackingRepository;
use App\Services\AIS\AISManager;
use Illuminate\Support\Facades\Log;

class TrackingService
{
    protected VesselRepository $vesselRepository;
    protected TrackingRepository $trackingRepository;
    protected AISManager $aisManager;

    public function __construct(
        VesselRepository $vesselRepository, 
        TrackingRepository $trackingRepository,
        AISManager $aisManager
    ) {
        $this->vesselRepository = $vesselRepository;
        $this->trackingRepository = $trackingRepository;
        $this->aisManager = $aisManager;
    }

    public function syncActiveVessels()
    {
        $vessels = $this->vesselRepository->getAllActiveVessels();
        $provider = $this->aisManager->provider();

        foreach ($vessels as $vessel) {
            try {
                $positionData = $provider->getVesselPosition($vessel->imo_number ?? $vessel->mmsi);
                
                if ($positionData) {
                    $positionData['vessel_id'] = $vessel->id;
                    $positionData['ais_provider'] = $provider->getName();
                    
                    $position = $this->trackingRepository->recordPosition($positionData);
                    
                    // Fire event to broadcast to Reverb
                    // event(new \App\Events\VesselMoved($position));
                }
            } catch (\Exception $e) {
                Log::error("Failed to sync vessel {$vessel->id}: " . $e->getMessage());
            }
        }
    }

    public function getLiveVesselData(string $vesselId)
    {
        $vessel = $this->vesselRepository->findById($vesselId);
        if (!$vessel) return null;

        $provider = $this->aisManager->provider();
        $livePosition = $provider->getVesselPosition($vessel->imo_number ?? $vessel->mmsi);

        if ($livePosition) {
            $livePosition['vessel_id'] = $vessel->id;
            $livePosition['ais_provider'] = $provider->getName();
            $this->trackingRepository->recordPosition($livePosition);
        } else {
            // If API fails, fallback to last known position from DB
            $lastKnown = $this->trackingRepository->getHistoricalPositions($vessel->id, 1)->first();
            if ($lastKnown) {
                $livePosition = $lastKnown->toArray();
            } else {
                return null;
            }
        }

        // Add history coordinates for the map polyline
        $history = $this->trackingRepository->getHistoricalPositions($vessel->id, 50);
        $livePosition['history'] = $history->map(function($pos) {
            return [(float)$pos->latitude, (float)$pos->longitude];
        })->toArray();

        // Add destination coordinates for planned route polyline
        $route = \App\Models\VesselRoute::with('destinationPort')->where('vessel_id', $vessel->id)->where('is_active', true)->first();
        if ($route && $route->destinationPort) {
            $livePosition['destination_coords'] = [
                (float)$route->destinationPort->latitude,
                (float)$route->destinationPort->longitude
            ];
            $livePosition['destination_name'] = $route->destinationPort->port_name;
        }

        return $livePosition;
    }
}
