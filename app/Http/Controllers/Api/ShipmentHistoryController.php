<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShipmentHistoryController extends Controller
{
    public function index(Request $request)
    {
        // Get shipment history
        return response()->json(['status' => 'success', 'data' => []]);
    }
}
