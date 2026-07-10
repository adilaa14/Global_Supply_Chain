<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardMetricService;
use Illuminate\Http\Request;

class DashboardMetricController extends Controller
{
    protected DashboardMetricService $metricService;

    public function __construct(DashboardMetricService $metricService)
    {
        $this->metricService = $metricService;
    }

    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        $keys = $request->input('keys', []); // Comma separated keys can be parsed here
        
        $metrics = $this->metricService->getMetrics($companyId, (array) $keys);

        return response()->json([
            'status' => 'success',
            'data' => $metrics
        ]);
    }
}
