<?php

namespace App\Services;

use App\Models\ShipmentStatusLog;
use App\Models\Shipment;

class ShipmentStatusService
{
    public function logStatusChange(Shipment $shipment, string $status, ?string $remarks, ?string $userId): ShipmentStatusLog
    {
        return ShipmentStatusLog::create([
            'shipment_id' => $shipment->id,
            'status' => $status,
            'remarks' => $remarks,
            'changed_by' => $userId,
        ]);
    }
}
