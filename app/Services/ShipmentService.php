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
        $cacheKey = "shipments.all.{$companyId}";
        
        return Cache::remember($cacheKey, 60, function () use ($companyId) {
            return $this->shipmentRepository->getAllShipments($companyId);
        });
    }

    public function getShipmentById(?string $companyId, string $id)
    {
        $cacheKey = "shipments.{$id}";
        
        return Cache::remember($cacheKey, 60, function () use ($companyId, $id) {
            return $this->shipmentRepository->findById($companyId, $id);
        });
    }

    public function clearCache(?string $companyId)
    {
        Cache::forget("shipments.all.{$companyId}");
    }
}
