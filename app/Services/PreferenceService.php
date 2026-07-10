<?php

namespace App\Services;

use App\Models\DashboardPreference;
use Illuminate\Support\Facades\Cache;

class PreferenceService
{
    public function getUserPreferences(string $userId)
    {
        return Cache::remember("dashboard.preferences.{$userId}", 3600, function () use ($userId) {
            return DashboardPreference::firstOrCreate(['user_id' => $userId]);
        });
    }

    public function updatePreferences(string $userId, array $data)
    {
        $preference = DashboardPreference::updateOrCreate(
            ['user_id' => $userId],
            $data
        );
        Cache::forget("dashboard.preferences.{$userId}");
        return $preference;
    }
}
