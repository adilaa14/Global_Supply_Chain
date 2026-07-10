<?php

namespace App\Services\Engines;

use App\Models\Shipment;
use Illuminate\Support\Facades\Log;

class ShipmentRedirectService
{
    protected RiskScoreService $riskScoreService;

    public function __construct(RiskScoreService $riskScoreService)
    {
        $this->riskScoreService = $riskScoreService;
    }

    /**
     * Analyzes if a shipment needs redirection based on dynamic risk thresholds.
     */
    public function evaluateRedirection(Shipment $shipment): ?array
    {
        $riskData = $this->riskScoreService->calculateShipmentRisk($shipment);

        // Configurable Thresholds (ideally fetched from system_settings)
        $thresholds = [
            'weather' => 80,
            'port'    => 70,
            'political' => 75,
            'currency' => 80
        ];

        $details = $riskData['details'];
        $needsRedirect = false;
        $reason = [];

        // Reverse the weighting to get the raw score out of 100
        if (($details['weather'] / 0.20) > $thresholds['weather']) {
            $needsRedirect = true;
            $reason[] = "Severe Weather Detected (Score: " . ($details['weather'] / 0.20) . ")";
        }
        
        if (($details['port'] / 0.20) > $thresholds['port']) {
            $needsRedirect = true;
            $reason[] = "High Port Congestion (Score: " . ($details['port'] / 0.20) . ")";
        }

        if (!$needsRedirect) {
            return null;
        }

        return $this->generateAlternativeRoute($shipment, implode(' | ', $reason));
    }

    protected function generateAlternativeRoute(Shipment $shipment, string $reason): array
    {
        // Complex AI logic to find the nearest safer port with low congestion
        // For demonstration, returning a structured recommendation
        
        Log::info("Generating redirect recommendation for Shipment {$shipment->shipment_code}");

        return [
            'alternative_port_id' => 'uuid-of-alternative-port',
            'alternative_country_id' => 'uuid-of-alternative-country',
            'recommendation_score' => 92,
            'estimated_delay_hours' => 24,
            'estimated_cost_increase' => 1500.00,
            'reason' => $reason,
        ];
    }
}
