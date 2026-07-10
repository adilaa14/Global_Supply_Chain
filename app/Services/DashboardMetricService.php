<?php

namespace App\Services;

use App\Repositories\DashboardRepository;
use Illuminate\Support\Facades\Cache;

class DashboardMetricService
{
    protected DashboardRepository $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getMetrics(?string $companyId, array $keys = [])
    {
        $cacheKey = 'dashboard.metrics.' . $companyId . '.' . md5(json_encode($keys));

        return Cache::remember($cacheKey, 60, function () use ($companyId, $keys) {
            return $this->dashboardRepository->getMetrics($companyId, $keys);
        });
    }
}
