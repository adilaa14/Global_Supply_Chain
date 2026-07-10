<?php

namespace App\Services\Engines;

use App\Models\Shipment;
use Illuminate\Support\Facades\Cache;

class RiskScoreService
{
    /**
     * Calculate comprehensive risk score for a shipment.
     * Weights: Weather 20%, Political 20%, Currency 10%, Port Congestion 20%, News 10%, History 10%, Seasonal 10%
     */
    public function calculateShipmentRisk(Shipment $shipment): array
    {
        // Cache the calculation for 10 minutes to avoid DB/API overload
        $cacheKey = "shipment_risk_score_{$shipment->id}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($shipment) {
            
            // In a real scenario, these would fetch from respective repositories/APIs
            $weatherRisk = $this->calculateWeatherRisk($shipment) * 0.20;
            $politicalRisk = $this->calculatePoliticalRisk($shipment->destination_country_id) * 0.20;
            $currencyRisk = $this->calculateCurrencyRisk($shipment->destination_country_id) * 0.10;
            $portRisk = $this->calculatePortCongestionRisk($shipment->destination_port_id) * 0.20;
            $newsRisk = $this->calculateNewsSentimentRisk($shipment) * 0.10;
            $historyRisk = $this->calculateHistoricalDelayRisk($shipment) * 0.10;
            $seasonalRisk = $this->calculateSeasonalRisk() * 0.10;

            $finalScore = (int) round(
                $weatherRisk + $politicalRisk + $currencyRisk + $portRisk + $newsRisk + $historyRisk + $seasonalRisk
            );

            // Ensure bounds 0-100
            $finalScore = max(0, min(100, $finalScore));

            return [
                'score'    => $finalScore,
                'category' => $this->getRiskCategory($finalScore),
                'details'  => [
                    'weather'    => $weatherRisk,
                    'political'  => $politicalRisk,
                    'currency'   => $currencyRisk,
                    'port'       => $portRisk,
                    'news'       => $newsRisk,
                    'history'    => $historyRisk,
                    'seasonal'   => $seasonalRisk,
                ]
            ];
        });
    }

    protected function getRiskCategory(int $score): string
    {
        if ($score <= 20) return 'Safe';
        if ($score <= 40) return 'Low';
        if ($score <= 60) return 'Medium';
        if ($score <= 80) return 'High';
        return 'Critical';
    }

    // Stub calculations that would read from configuration/database
    protected function calculateWeatherRisk(Shipment $shipment): int { return random_int(10, 80); }
    protected function calculatePoliticalRisk(?string $countryId): int { return 30; }
    protected function calculateCurrencyRisk(?string $countryId): int { return 25; }
    protected function calculatePortCongestionRisk(?string $portId): int { return 65; }
    protected function calculateNewsSentimentRisk(Shipment $shipment): int { return 20; }
    protected function calculateHistoricalDelayRisk(Shipment $shipment): int { return 15; }
    protected function calculateSeasonalRisk(): int { return 50; }
}
