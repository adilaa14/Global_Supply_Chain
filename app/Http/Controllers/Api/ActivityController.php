<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    protected ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        $limit = $request->input('limit', 50);
        
        $activities = $this->activityService->getRecentTimeline($companyId, $limit);

        return response()->json([
            'status' => 'success',
            'data' => $activities
        ]);
    }
}
