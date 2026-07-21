<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShipmentService;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    protected ShipmentService $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    public function index(Request $request)
    {
        $shipments = $this->shipmentService->getAllShipments($request->user()->company_id);
        
        return response()->json([
            'status' => 'success',
            'data' => $shipments
        ]);
    }

    public function show(Request $request, string $id)
    {
        $shipment = $this->shipmentService->getShipmentById($request->user()->company_id, $id);
        
        return response()->json([
            'status' => 'success',
            'data' => $shipment
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'shipment_number' => 'required|string|max:255|unique:shipments,shipment_number',
            'shipment_type' => 'required|string',
            'origin_port_id' => 'required|string|exists:ports,id',
            'destination_port_id' => 'required|string|exists:ports,id',
            'vessel_id' => 'required|string|exists:vessels,id',
            'commodity_id' => 'required|string|exists:commodities,id',
            'priority' => 'nullable|string',
        ]);

        $companyId = $request->user()->company_id;
        $originPort = \App\Models\Port::find($data['origin_port_id']);
        $destPort = \App\Models\Port::find($data['destination_port_id']);
        
        $shipmentData = [
            'company_id' => $companyId,
            'shipment_number' => $data['shipment_number'],
            'shipment_type' => $data['shipment_type'],
            'status' => 'Preparing',
            'origin_country_id' => $originPort ? $originPort->country_id : null,
            'destination_country_id' => $destPort ? $destPort->country_id : null,
            'origin_port_id' => $data['origin_port_id'],
            'destination_port_id' => $data['destination_port_id'],
            'commodity_id' => $data['commodity_id'],
            'vessel_id' => $data['vessel_id'],
            'estimated_arrival' => now()->addDays(14),
            'quantity' => 100,
        ];

        $shipment = $this->shipmentService->createShipment($shipmentData);

        // Instruct the Global Fleet Engine to redirect the vessel!
        if (!empty($data['vessel_id'])) {
            try {
                $redirectRequest = new Request([
                    'port_id' => $data['destination_port_id'],
                    'origin_port_id' => $data['origin_port_id']
                ]);
                
                // Update shipment status to In Transit after assigning
                $shipment->update(['status' => 'In Transit']);
                app(\App\Http\Controllers\Api\VesselController::class)->redirectVessel($redirectRequest, $data['vessel_id']);
            } catch (\Exception $e) {
                // Log exception if vessel routing fails, but don't crash shipment creation
                \Illuminate\Support\Facades\Log::error("Vessel redirect failed: " . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success', 
            'data' => $shipment,
            'message' => 'Shipment created successfully and vessel redirected.'
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $shipment = \App\Models\Shipment::findOrFail($id);

        $data = $request->validate([
            'status' => 'sometimes|string',
            'weight' => 'sometimes|nullable|numeric',
            'estimated_value' => 'sometimes|nullable|numeric',
            'quantity' => 'sometimes|nullable|numeric',
        ]);
        
        $shipment->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $shipment,
            'message' => 'Shipment updated successfully.'
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        // ... Logic to soft delete shipment
        return response()->json(['status' => 'success']);
    }
}
