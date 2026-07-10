<?php

namespace App\Listeners;

use App\Events\ShipmentCreated;
use App\Services\DashboardService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RefreshDashboard implements ShouldQueue
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function handle(object $event): void
    {
        if (property_exists($event, 'companyId')) {
            $this->dashboardService->clearCache($event->companyId);
        }
    }
}
