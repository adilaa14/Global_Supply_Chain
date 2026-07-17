<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShipmentContainerController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'shipment_id' => 'required|exists:shipments,id',
            'container_number' => 'required|string',
            'container_type' => 'required|string',
            'container_size' => 'required|string',
        ]);
        
        // Use Model directly for simplicity
        $containerId = (string) \Illuminate\Support\Str::uuid();
        
        \Illuminate\Support\Facades\DB::table('shipment_containers')->insert([
            'id' => $containerId,
            'shipment_id' => $data['shipment_id'],
            'container_number' => $data['container_number'],
            'container_type' => $data['container_type'],
            'container_size' => $data['container_size'],
            'seal_number' => 'SEAL-' . rand(1000, 9999), // Mock seal number
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update container count
        \Illuminate\Support\Facades\DB::table('shipments')
            ->where('id', $data['shipment_id'])
            ->increment('container_count');

        $newContainer = \Illuminate\Support\Facades\DB::table('shipment_containers')->where('id', $containerId)->first();

        return response()->json([
            'status' => 'success', 
            'data' => $newContainer,
            'message' => 'Container added successfully.'
        ], 201);
    }
}
