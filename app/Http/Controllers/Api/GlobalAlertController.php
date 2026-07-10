<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AlertService;
use Illuminate\Http\Request;

class GlobalAlertController extends Controller
{
    protected AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $alerts = $this->alertService->getGlobalAlerts($limit);

        return response()->json([
            'status' => 'success',
            'data' => $alerts
        ]);
    }
}
