<?php

namespace App\Services;

use App\Repositories\VesselRepository;
use App\Repositories\TrackingRepository;

class MapService
{
    protected VesselRepository $vesselRepository;
    protected TrackingRepository $trackingRepository;

    public function __construct(
        VesselRepository $vesselRepository,
        TrackingRepository $trackingRepository
    ) {
        $this->vesselRepository = $vesselRepository;
        $this->trackingRepository = $trackingRepository;
    }

    public function getGlobalMapData()
    {
        $vessels = $this->vesselRepository->getAllActiveVessels();
        
        $mapData = $vessels->map(function ($vessel) {
            $latestPos = $vessel->latestPosition;
            
            return [
                'id' => $vessel->id,
                'name' => $vessel->name,
                'type' => $vessel->vessel_type,
                'status' => $vessel->status,
                'position' => $latestPos ? [
                    'lat' => $latestPos->latitude,
                    'lng' => $latestPos->longitude,
                    'speed' => $latestPos->speed,
                    'heading' => $latestPos->heading,
                    'timestamp' => $latestPos->timestamp,
                ] : null,
                'destination' => $vessel->activeRoute?->destinationPort?->port_name ?? 'Unknown',
                'eta' => $vessel->activeRoute?->estimated_arrival ?? null,
            ];
        });

        return [
            'vessels' => $mapData->filter(fn($v) => $v['position'] !== null)->values(),
            // In a real scenario, weather and ports would be added here
            'weather_layers' => [],
            'ports' => []
        ];
    }
}
