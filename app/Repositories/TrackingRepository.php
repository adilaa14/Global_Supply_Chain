<?php

namespace App\Repositories;

use App\Models\VesselPosition;
use App\Models\TrackingEvent;
use App\Models\TrackingHistory;

class TrackingRepository
{
    public function recordPosition(array $data): VesselPosition
    {
        return VesselPosition::create($data);
    }

    public function getLatestPositions(array $vesselIds)
    {
        return VesselPosition::whereIn('vessel_id', $vesselIds)
            ->whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')
                      ->from('vessel_positions')
                      ->groupBy('vessel_id');
            })
            ->get();
    }

    public function getHistoricalPositions(string $vesselId, int $limit = 100)
    {
        return VesselPosition::where('vessel_id', $vesselId)
            ->orderBy('timestamp', 'desc')
            ->limit($limit)
            ->get();
    }

    public function recordEvent(array $data): TrackingEvent
    {
        return TrackingEvent::create($data);
    }

    public function recordHistory(array $data): TrackingHistory
    {
        return TrackingHistory::create($data);
    }
}
