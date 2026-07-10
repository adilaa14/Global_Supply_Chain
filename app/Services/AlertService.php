<?php

namespace App\Services;

use App\Repositories\AlertRepository;
use Illuminate\Support\Facades\Cache;

class AlertService
{
    protected AlertRepository $alertRepository;

    public function __construct(AlertRepository $alertRepository)
    {
        $this->alertRepository = $alertRepository;
    }

    public function getGlobalAlerts(int $limit = 10)
    {
        $cacheKey = "dashboard.alerts.global.{$limit}";

        return Cache::remember($cacheKey, 300, function () use ($limit) {
            return $this->alertRepository->getActiveAlerts($limit);
        });
    }
}
