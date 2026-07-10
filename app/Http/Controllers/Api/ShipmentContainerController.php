<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShipmentContainerController extends Controller
{
    public function store(Request $request)
    {
        // Container creation logic
        return response()->json(['status' => 'success', 'data' => []], 201);
    }
}
