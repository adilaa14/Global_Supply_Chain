<?php

namespace App\Repositories;

use App\Models\GlobalAlert;

class AlertRepository
{
    public function getActiveAlerts(int $limit = 10)
    {
        return GlobalAlert::where('is_active', true)
            ->orderByRaw("FIELD(severity, 'Critical', 'High', 'Medium', 'Low')")
            ->latest()
            ->limit($limit)
            ->get();
    }
}
