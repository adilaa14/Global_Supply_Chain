<?php

namespace App\Services;

use App\Repositories\ActivityRepository;
use Illuminate\Support\Facades\Cache;

class ActivityService
{
    protected ActivityRepository $activityRepository;

    public function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function getRecentTimeline(?string $companyId, int $limit = 50)
    {
        $cacheKey = "dashboard.timeline.{$companyId}.{$limit}";

        return Cache::remember($cacheKey, 60, function () use ($companyId, $limit) {
            return $this->activityRepository->getRecentActivities($companyId, $limit);
        });
    }

    public function logActivity(?string $companyId, string $type, string $title, ?string $description = null, array $metaData = [])
    {
        $activity = $this->activityRepository->createActivity($companyId, $type, $title, $description, $metaData);
        Cache::forget("dashboard.timeline.{$companyId}.50");
        return $activity;
    }
}
