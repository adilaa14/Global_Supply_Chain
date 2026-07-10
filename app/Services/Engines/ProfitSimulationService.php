<?php

namespace App\Services\Engines;

use App\Models\Shipment;
use App\Models\Commodity;
use Illuminate\Support\Facades\Log;

class ProfitSimulationService
{
    /**
     * Simulate profit difference before a shipment redirection.
     * 
     * Profit = Commodity Revenue - Shipping Cost - Fuel Cost - Insurance - Tax - Port Charges
     */
    public function simulateRedirect(Shipment $shipment, array $alternativeData): array
    {
        // 1. Current Destination Economics
        $currentRevenue = $this->calculateExpectedRevenue($shipment);
        $currentCost = $this->calculateTotalCost(
            $shipment->shipping_cost,
            $shipment->insurance_cost,
            $shipment->cargo_weight // Used for tax/port charge estimation
        );
        $currentProfit = $currentRevenue - $currentCost;

        // 2. Alternative Destination Economics
        // Fetch new price based on alternative destination (mocked here for architecture)
        $alternativeCommodityPrice = $this->getMarketPriceAtDestination(
            $shipment->commodity_id, 
            $alternativeData['country_id']
        );
        $alternativeRevenue = ($shipment->cargo_weight ?? 1) * $alternativeCommodityPrice;
        
        $alternativeCost = $this->calculateTotalCost(
            $shipment->shipping_cost + $alternativeData['additional_shipping_cost'],
            $shipment->insurance_cost + $alternativeData['additional_insurance_cost'],
            $shipment->cargo_weight
        );
        $alternativeProfit = $alternativeRevenue - $alternativeCost;

        // 3. Profit Delta Calculation
        $additionalProfit = $alternativeProfit - $currentProfit;

        Log::info("Profit simulation ran for Shipment {$shipment->shipment_code}. Delta: {$additionalProfit}");

        return [
            'current' => [
                'revenue' => $currentRevenue,
                'cost'    => $currentCost,
                'profit'  => $currentProfit,
            ],
            'alternative' => [
                'revenue' => $alternativeRevenue,
                'cost'    => $alternativeCost,
                'profit'  => $alternativeProfit,
            ],
            'insight' => [
                'additional_profit' => $additionalProfit,
                'recommendation' => $additionalProfit > 0 
                                    ? 'Strongly Recommended (Higher Profit Potential)' 
                                    : 'Avoid (Results in Loss)',
            ]
        ];
    }

    protected function calculateExpectedRevenue(Shipment $shipment): float
    {
        if (!$shipment->commodity_id) return 0.0;
        
        // Assume cargo_weight acts as unit multiplier
        $price = Commodity::find($shipment->commodity_id)?->current_price ?? 0;
        return ($shipment->cargo_weight ?? 1) * $price;
    }

    protected function calculateTotalCost(float $shipping, float $insurance, ?float $weight): float
    {
        $taxEstimate = ($weight ?? 100) * 0.05; // 5% generic tax estimation
        $portCharges = 500.00; // Flat port fee

        return $shipping + $insurance + $taxEstimate + $portCharges;
    }

    protected function getMarketPriceAtDestination(?string $commodityId, string $countryId): float
    {
        // In a real scenario, queries a 'country_commodity_prices' table.
        // Returning a generic markup for demonstration of the business logic.
        $basePrice = Commodity::find($commodityId)?->current_price ?? 1000;
        return $basePrice * 1.15; // 15% higher demand
    }
}
