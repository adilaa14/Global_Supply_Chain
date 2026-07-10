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
        // Unified dashboard search logic here
        return response()->json([
            'status' => 'success',
            'data' => [] // Implement full text search logic
        ]);
    }
}
