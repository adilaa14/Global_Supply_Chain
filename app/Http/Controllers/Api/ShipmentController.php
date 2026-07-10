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
        // ... Logic to store shipment
        return response()->json(['status' => 'success', 'data' => []], 201);
    }

    public function update(Request $request, string $id)
    {
        // ... Logic to update shipment
        return response()->json(['status' => 'success', 'data' => []]);
    }

    public function destroy(Request $request, string $id)
    {
        // ... Logic to soft delete shipment
        return response()->json(['status' => 'success']);
    }
}
