<?php

namespace App\Repositories;

use App\Models\DashboardSnapshot;
use App\Models\DashboardMetric;

class DashboardRepository
{
    public function getLatestSnapshot(?string $companyId)
    {
        return DashboardSnapshot::where('company_id', $companyId)
            ->latest('snapshot_date')
            ->first();
    }

    public function getMetrics(?string $companyId, array $keys = [])
    {
        $query = DashboardMetric::where('company_id', $companyId);
        
        if (!empty($keys)) {
            $query->whereIn('metric_key', $keys);
        }

        return $query->get();
    }

    public function updateMetric(?string $companyId, string $key, array $data)
    {
        return DashboardMetric::updateOrCreate(
            ['company_id' => $companyId, 'metric_key' => $key],
            array_merge($data, ['calculated_at' => now()])
        );
    }
}
