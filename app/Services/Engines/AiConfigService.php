<?php

namespace App\Services\Engines;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class AiConfigService
{
    /**
     * Retrieve a configuration threshold for AI calculations.
     * Prevents hardcoding by pulling from the database 'settings' table.
     * Includes caching for maximum performance.
     */
    public function getThreshold(string $key, $default = 0)
    {
        return Cache::remember("ai_threshold_{$key}", now()->addHours(24), function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? (float) $setting->value : $default;
        });
    }

    /**
     * Helper to get risk thresholds
     */
    public function getRiskThresholds(): array
    {
        return [
            'weather_high' => $this->getThreshold('ai_risk_weather_high', 75),
            'political_high' => $this->getThreshold('ai_risk_political_high', 80),
            'port_congestion_high' => $this->getThreshold('ai_risk_port_congestion_high', 70),
        ];
    }

    /**
     * Helper to get profit simulation thresholds
     */
    public function getProfitThresholds(): array
    {
        return [
            'minimum_margin_redirect' => $this->getThreshold('ai_profit_min_margin_redirect', 10), // 10%
            'price_drop_trigger' => $this->getThreshold('ai_profit_price_drop_trigger', 10), // 10% drop triggers alert
        ];
    }
}
