<?php

namespace App\Repositories;

use App\Models\Shipment;

class ShipmentRepository
{
    public function getAllShipments(?string $companyId)
    {
        return Shipment::where('company_id', $companyId)
            ->with(['items', 'containers'])
            ->latest()
            ->paginate(15);
    }

    public function findById(?string $companyId, string $id)
    {
        return Shipment::where('company_id', $companyId)
            ->with(['items', 'containers', 'documents', 'histories', 'statusLogs', 'notes', 'tags'])
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return Shipment::create($data);
    }

    public function update(Shipment $shipment, array $data)
    {
        $shipment->update($data);
        return $shipment;
    }

    public function delete(Shipment $shipment)
    {
        return $shipment->delete();
    }
}
