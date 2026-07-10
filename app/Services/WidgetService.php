<?php

namespace App\Services;

use App\Models\DashboardWidget;
use Illuminate\Support\Facades\Cache;

class WidgetService
{
    public function getUserWidgets(string $userId)
    {
        return Cache::remember("dashboard.widgets.{$userId}", 3600, function () use ($userId) {
            return DashboardWidget::where('user_id', $userId)
                ->orderBy('order')
                ->get();
        });
    }

    public function updateWidget(string $userId, string $widgetId, array $data)
    {
        $widget = DashboardWidget::where('user_id', $userId)->findOrFail($widgetId);
        $widget->update($data);
        Cache::forget("dashboard.widgets.{$userId}");
        return $widget;
    }

    public function reorderWidgets(string $userId, array $orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            DashboardWidget::where('user_id', $userId)
                ->where('id', $id)
                ->update(['order' => $index]);
        }
        Cache::forget("dashboard.widgets.{$userId}");
    }
}
