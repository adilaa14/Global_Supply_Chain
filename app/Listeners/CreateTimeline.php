<?php

namespace App\Listeners;

use App\Events\ShipmentCreated;
use App\Services\ActivityService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateTimeline implements ShouldQueue
{
    protected ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    public function handle(object $event): void
    {
        if ($event instanceof ShipmentCreated) {
            $this->activityService->logActivity(
                $event->companyId,
                'ShipmentCreated',
                'New Shipment Created',
                "Shipment {$event->shipmentId} was successfully initialized.",
                ['shipment_id' => $event->shipmentId]
            );
        }
    }
}
