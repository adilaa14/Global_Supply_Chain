<?php

namespace App\Services;

use App\Repositories\DashboardRepository;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    protected DashboardRepository $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getDashboardSummary(?string $companyId)
    {
        $cacheKey = "dashboard.summary.{$companyId}";
        
        return Cache::remember($cacheKey, 300, function () use ($companyId) {
            $snapshot = $this->dashboardRepository->getLatestSnapshot($companyId);
            $metrics = $this->dashboardRepository->getMetrics($companyId);
            
            $mappedMetrics = $metrics->mapWithKeys(function ($item) {
                return [$item->metric_key => $item->numeric_value ?? $item->string_value ?? $item->json_value];
            })->toArray();

            return [
                'snapshot' => $snapshot,
                'metrics' => $mappedMetrics,
            ];
        });
    }

    public function clearCache(?string $companyId)
    {
        Cache::forget("dashboard.summary.{$companyId}");
    }
}
