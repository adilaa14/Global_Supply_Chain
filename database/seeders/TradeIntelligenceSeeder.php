<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Commodity;
use App\Models\TradeOpportunity;
use App\Models\TradeSimulation;
use App\Models\TradeForecast;
use App\Models\TradeInsight;
use App\Models\TradeMarketAnalysis;
use App\Models\TradeRiskAnalysis;
use App\Models\AlternativeDestination;
use App\Models\User;

class TradeIntelligenceSeeder extends Seeder
{
    public function run(): void
    {
        $countries = Country::all();
        $commodities = Commodity::all();
        $admin = User::first();

        if ($countries->count() === 0 || $commodities->count() === 0) {
            return;
        }

        foreach ($countries as $country) {
            // Seed Trade Risk Analysis
            TradeRiskAnalysis::create([
                'country_id' => $country->id,
                'political_risk' => rand(1, 100),
                'currency_risk' => rand(1, 100),
                'economic_risk' => rand(1, 100),
                'weather_risk' => rand(1, 100),
                'shipping_risk' => rand(1, 100),
                'port_congestion' => rand(1, 100),
                'commodity_volatility' => rand(1, 100),
                'trade_restriction' => rand(1, 100),
                'total_risk_score' => rand(1, 100),
            ]);

            // For each country, pick 3 random commodities to analyze
            $selectedCommodities = $commodities->random(3);
            
            foreach ($selectedCommodities as $commodity) {
                // Trade Market Analysis
                TradeMarketAnalysis::create([
                    'country_id' => $country->id,
                    'commodity_id' => $commodity->id,
                    'demand_score' => rand(10, 100),
                    'supply_score' => rand(10, 100),
                    'margin_score' => rand(10, 100),
                    'growth_score' => rand(10, 100),
                    'is_emerging_market' => rand(0, 1) == 1,
                ]);

                // Trade Opportunity
                TradeOpportunity::create([
                    'country_id' => $country->id,
                    'commodity_id' => $commodity->id,
                    'opportunity_score' => rand(50, 100),
                    'estimated_profit' => rand(10000, 1000000),
                    'reason' => 'High demand and low supply predicted for the next quarter.',
                ]);

                // Trade Forecast (Next 3 months)
                for ($i = 1; $i <= 3; $i++) {
                    TradeForecast::create([
                        'country_id' => $country->id,
                        'commodity_id' => $commodity->id,
                        'forecast_date' => now()->addMonths($i),
                        'predicted_demand' => rand(10000, 50000),
                        'predicted_supply' => rand(5000, 40000),
                        'predicted_price' => rand(100, 5000),
                        'predicted_profit' => rand(1000, 50000),
                        'market_trend' => collect(['Up', 'Down', 'Stable'])->random(),
                        'country_opportunity_score' => rand(50, 100),
                    ]);
                }
            }
        }

        // Seed some simulations
        for ($i = 0; $i < 5; $i++) {
            $origin = $countries->random();
            $dest = $countries->except($origin->id)->random();
            $commodity = $commodities->random();

            TradeSimulation::create([
                'user_id' => $admin ? $admin->id : null,
                'commodity_id' => $commodity->id,
                'origin_country_id' => $origin->id,
                'destination_country_id' => $dest->id,
                'quantity' => rand(10, 100),
                'container_type' => '40ft Standard',
                'shipping_cost' => rand(2000, 5000),
                'insurance' => rand(500, 1000),
                'import_tax' => rand(1000, 3000),
                'export_tax' => rand(500, 1500),
                'currency' => 'USD',
                'revenue' => rand(50000, 150000),
                'cost' => rand(30000, 40000),
                'profit' => rand(10000, 50000),
                'margin' => rand(10, 40),
                'roi' => rand(15, 60),
                'break_even_point' => rand(5, 20),
            ]);
        }

        // Seed Alternative Destinations
        for ($i = 0; $i < 5; $i++) {
            $original = $countries->random();
            $alt = $countries->except($original->id)->random();
            
            AlternativeDestination::create([
                'original_destination_id' => $original->id,
                'alternative_country_id' => $alt->id,
                'commodity_id' => $commodities->random()->id,
                'reason' => 'Lower import taxes and shorter ETA.',
                'price_difference' => rand(100, 1000),
                'demand_difference' => rand(500, 2000),
                'shipping_cost_difference' => rand(-500, -100),
                'risk_difference' => rand(-20, -5),
                'eta_difference_days' => rand(-10, -2),
                'profit_difference' => rand(5000, 20000),
            ]);
        }

        // Seed Insights
        TradeInsight::create([
            'type' => 'Best Country',
            'title' => 'Singapore emerges as top hub',
            'description' => 'Due to recent policy changes, Singapore is the most profitable destination for electronics.',
            'metadata' => json_encode(['country_id' => $countries->first()->id]),
        ]);
        
        TradeInsight::create([
            'type' => 'Biggest Risk',
            'title' => 'Port Congestion in LA',
            'description' => 'Severe delays expected in West Coast ports impacting trans-Pacific routes.',
            'metadata' => json_encode(['severity' => 'High']),
        ]);
    }
}
