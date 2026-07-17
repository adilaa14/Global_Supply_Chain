<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function summary(Request $request)
    {
        $companyId = $request->user()->company_id;
        
        $summary = $this->dashboardService->getDashboardSummary($companyId);
        
        return response()->json([
            'status' => 'success',
            'data' => $summary
        ]);
    }

    public function search(Request $request)
    {
        $companyId = $request->user()->company_id;
        $query = $request->input('q');

        if (empty($query)) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }

        // Search through shipments for the company
        $shipments = \App\Models\Shipment::with(['originPort', 'destinationPort', 'vessel'])
            ->where('company_id', $companyId)
            ->where(function($q) use ($query) {
                $q->where('tracking_number', 'like', "%{$query}%")
                  ->orWhere('status', 'like', "%{$query}%")
                  ->orWhereHas('originPort', function($subQ) use ($query) {
                      $subQ->where('port_name', 'like', "%{$query}%");
                  })
                  ->orWhereHas('destinationPort', function($subQ) use ($query) {
                      $subQ->where('port_name', 'like', "%{$query}%");
                  })
                  ->orWhereHas('vessel', function($subQ) use ($query) {
                      $subQ->where('name', 'like', "%{$query}%");
                  });
            })
            ->limit(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $shipments
        ]);
    }
}
