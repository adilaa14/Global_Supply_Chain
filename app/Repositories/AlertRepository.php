<?php

namespace App\Repositories;

use App\Models\GlobalAlert;

class AlertRepository
{
    public function getActiveAlerts(int $limit = 10)
    {
        return GlobalAlert::where('is_active', true)
            ->orderByRaw("CASE severity WHEN 'Critical' THEN 1 WHEN 'High' THEN 2 WHEN 'Medium' THEN 3 WHEN 'Low' THEN 4 ELSE 5 END")
            ->latest()
            ->limit($limit)
            ->get();
    }
}
