<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PreferenceService;
use Illuminate\Http\Request;

class DashboardPreferenceController extends Controller
{
    protected PreferenceService $preferenceService;

    public function __construct(PreferenceService $preferenceService)
    {
        $this->preferenceService = $preferenceService;
    }

    public function index(Request $request)
    {
        $preferences = $this->preferenceService->getUserPreferences($request->user()->id);
        
        return response()->json([
            'status' => 'success',
            'data' => $preferences
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'layout_id' => 'string',
            'visible_widgets' => 'array',
            'favorite_countries' => 'array',
            'favorite_commodities' => 'array',
            'favorite_routes' => 'array',
            'default_filters' => 'array',
        ]);
        
        $preferences = $this->preferenceService->updatePreferences($request->user()->id, $validated);
        
        return response()->json([
            'status' => 'success',
            'data' => $preferences
        ]);
    }
}
