<?php

namespace App\Services;

use App\Repositories\ShipmentRepositoryInterface;
use App\Models\Shipment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentService
{
    protected ShipmentRepositoryInterface $shipmentRepository;

    public function __construct(ShipmentRepositoryInterface $shipmentRepository)
    {
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * Create a new shipment with strict business logic.
     */
    public function createShipment(array $data): Shipment
    {
        DB::beginTransaction();
        try {
            // Business Logic: Generate tracking code
            $data['shipment_code'] = 'SHP-' . strtoupper(uniqid());
            $data['status'] = 'preparing';
            $data['risk_score'] = 0; // Default until async RiskService evaluates

            $shipment = $this->shipmentRepository->create($data);

            // Trigger events, notifications etc (e.g. event(new ShipmentCreated($shipment)))
            
            DB::commit();
            return $shipment;
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create shipment: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getShipmentDetails(string $uuid)
    {
        return $this->shipmentRepository->findById($uuid);
    }
    
    // other enterprise features...
}
