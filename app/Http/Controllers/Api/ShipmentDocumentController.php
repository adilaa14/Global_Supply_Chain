<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShipmentDocumentController extends Controller
{
    public function store(Request $request)
    {
        // Document upload logic
        return response()->json(['status' => 'success', 'data' => []], 201);
    }
}
