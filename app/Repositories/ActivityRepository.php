<?php

namespace App\Repositories;

use App\Models\ActivityTimeline;

class ActivityRepository
{
    public function getRecentActivities(?string $companyId, int $limit = 50)
    {
        return ActivityTimeline::where('company_id', $companyId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function createActivity(?string $companyId, string $type, string $title, ?string $description = null, array $metaData = [])
    {
        return ActivityTimeline::create([
            'company_id' => $companyId,
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'meta_data' => $metaData,
        ]);
    }
}
