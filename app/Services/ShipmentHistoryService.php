<?php

namespace App\Services;

use App\Models\ShipmentHistory;
use App\Models\Shipment;

class ShipmentHistoryService
{
    public function logHistory(Shipment $shipment, string $field, ?string $oldValue, ?string $newValue, ?string $userId): ShipmentHistory
    {
        return ShipmentHistory::create([
            'shipment_id' => $shipment->id,
            'field_changed' => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'changed_by' => $userId,
        ]);
    }
}
