<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ShipmentWebController extends Controller
{
    public function index(Request $request, \App\Services\ShipmentService $shipmentService)
    {
        $shipments = $shipmentService->getAllShipments($request->user()->company_id);
        
        return Inertia::render('Shipment/Index', [
            'initialShipments' => $shipments->items()
        ]);
    }

    public function create()
    {
        return Inertia::render('Shipment/Create');
    }

    public function show(string $id)
    {
        return Inertia::render('Shipment/Show', [
            'shipmentId' => $id
        ]);
    }
}
