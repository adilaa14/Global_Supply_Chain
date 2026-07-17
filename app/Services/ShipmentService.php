<?php

namespace App\Services;

use App\Repositories\ShipmentRepository;
use Illuminate\Support\Facades\Cache;

class ShipmentService
{
    protected ShipmentRepository $shipmentRepository;

    public function __construct(ShipmentRepository $shipmentRepository)
    {
        $this->shipmentRepository = $shipmentRepository;
    }

    public function getAllShipments(?string $companyId)
    {
        return $this->shipmentRepository->getAllShipments($companyId);
    }

    public function getShipmentById(?string $companyId, string $id)
    {
        return $this->shipmentRepository->findById($companyId, $id);
    }

    public function createShipment(array $data)
    {
        $shipment = $this->shipmentRepository->create($data);
        $this->clearCache($data['company_id']);
        return $shipment;
    }

    public function clearCache(?string $companyId)
    {
        Cache::forget("shipments.all.{$companyId}");
    }
}
