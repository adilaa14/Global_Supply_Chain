<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WidgetService;
use Illuminate\Http\Request;

class DashboardWidgetController extends Controller
{
    protected WidgetService $widgetService;

    public function __construct(WidgetService $widgetService)
    {
        $this->widgetService = $widgetService;
    }

    public function index(Request $request)
    {
        $widgets = $this->widgetService->getUserWidgets($request->user()->id);
        
        return response()->json([
            'status' => 'success',
            'data' => $widgets
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'widget_key' => 'required|string',
            'order' => 'integer',
            'is_enabled' => 'boolean',
            'settings' => 'array',
        ]);
        
        $validated['user_id'] = $request->user()->id;
        
        $widget = \App\Models\DashboardWidget::create($validated);
        
        return response()->json([
            'status' => 'success',
            'data' => $widget
        ]);
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'required|uuid'
        ]);

        $this->widgetService->reorderWidgets($request->user()->id, $validated['ordered_ids']);

        return response()->json([
            'status' => 'success'
        ]);
    }
}
