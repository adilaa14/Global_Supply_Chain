<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommodityIntelligenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define Categories
        $categories = [
            'Energy' => ['Crude Oil', 'Natural Gas', 'Coal'],
            'Agriculture' => ['Palm Oil', 'Rubber', 'Rice', 'Sugar', 'Coffee', 'Tea', 'Corn', 'Wheat', 'Soybean', 'Cotton', 'Cocoa'],
            'Metals' => ['Nickel', 'Copper', 'Gold', 'Silver', 'Tin', 'Iron Ore', 'Steel', 'Aluminium', 'Lithium'],
            'Others' => ['Fertilizer', 'Seafood', 'Livestock']
        ];

        $units = [
            'Crude Oil' => 'Barrel', 'Natural Gas' => 'MMBtu', 'Coal' => 'Ton',
            'Palm Oil' => 'Ton', 'Rubber' => 'Kg', 'Rice' => 'Ton', 'Sugar' => 'Kg',
            'Coffee' => 'Kg', 'Tea' => 'Kg', 'Corn' => 'Ton', 'Wheat' => 'Ton', 'Soybean' => 'Ton', 'Cotton' => 'Kg', 'Cocoa' => 'Ton',
            'Nickel' => 'Ton', 'Copper' => 'Ton', 'Gold' => 'Ounce', 'Silver' => 'Ounce',
            'Tin' => 'Ton', 'Iron Ore' => 'Ton', 'Steel' => 'Ton', 'Aluminium' => 'Ton', 'Lithium' => 'Ton',
            'Fertilizer' => 'Ton', 'Seafood' => 'Kg', 'Livestock' => 'Head'
        ];

        // Seed Categories & Commodities
        foreach ($categories as $catName => $items) {
            $category = \App\Models\CommodityCategory::create([
                'name' => $catName,
                'description' => "Global $catName Market"
            ]);

            foreach ($items as $itemName) {
                $commodity = \App\Models\Commodity::create([
                    'category_id' => $category->id,
                    'commodity_name' => $itemName,
                    'commodity_code' => rand(1000, 9999) . '.' . rand(10, 99),
                    'unit' => $units[$itemName] ?? 'Unit',
                    'description' => "Global market data for $itemName",
                ]);

                // Create Prices
                $basePrice = rand(50, 5000);
                \App\Models\CommodityPrice::create([
                    'commodity_id' => $commodity->id,
                    'current_price' => $basePrice,
                    'open_price' => $basePrice - rand(1, 10),
                    'close_price' => $basePrice + rand(1, 10),
                    'high' => $basePrice + rand(10, 50),
                    'low' => $basePrice - rand(10, 50),
                    'average' => $basePrice,
                    'moving_average' => $basePrice,
                    'price_change' => rand(-500, 500) / 100, // -5.00 to 5.00 %
                    'daily_change' => rand(-100, 100) / 100,
                    'weekly_change' => rand(-200, 200) / 100,
                    'monthly_change' => rand(-500, 500) / 100,
                    'yearly_change' => rand(-1000, 1000) / 100,
                    'volatility' => rand(100, 500) / 100,
                    'trend' => ['Up', 'Down', 'Stable'][rand(0, 2)],
                    'last_calculated_at' => now()
                ]);

                // Create Histories (last 30 days for demo)
                for ($i = 30; $i >= 0; $i--) {
                    \App\Models\CommodityPriceHistory::create([
                        'commodity_id' => $commodity->id,
                        'date' => now()->subDays($i)->format('Y-m-d'),
                        'price' => $basePrice + rand(-100, 100),
                    ]);
                }

                // Create Market
                \App\Models\CommodityMarket::create([
                    'commodity_id' => $commodity->id,
                    'global_demand' => rand(1000000, 9999999),
                    'global_supply' => rand(1000000, 9999999),
                    'top_exporting_countries' => ['USA', 'China', 'Brazil', 'Australia', 'Indonesia'],
                    'top_importing_countries' => ['China', 'USA', 'India', 'Japan', 'Germany'],
                    'major_producers' => ['Company A', 'Company B', 'Company C'],
                    'major_consumers' => ['Industry X', 'Industry Y'],
                    'market_share' => rand(10, 100) / 10,
                    'price_trend' => ['Upward', 'Downward', 'Stable'][rand(0, 2)]
                ]);

                // Create Demand
                \App\Models\CommodityDemand::create([
                    'commodity_id' => $commodity->id,
                    'current_demand' => rand(1000000, 9999999),
                    'demand_score' => rand(50, 100),
                    'demand_growth' => rand(100, 500) / 100,
                    'top_buyers' => ['Country A', 'Country B'],
                    'emerging_markets' => ['Country C', 'Country D'],
                    'consumption_trend' => 'Increasing',
                    'year' => date('Y')
                ]);

                // Create Supply
                \App\Models\CommoditySupply::create([
                    'commodity_id' => $commodity->id,
                    'current_supply' => rand(1000000, 9999999),
                    'supply_score' => rand(50, 100),
                    'supply_growth' => rand(100, 500) / 100,
                    'production_volume' => rand(1000000, 9999999),
                    'stock_level' => rand(500000, 1000000),
                    'major_producers' => ['Producer 1', 'Producer 2'],
                    'year' => date('Y')
                ]);

                // Create Ranking
                \App\Models\CommodityRanking::create([
                    'commodity_id' => $commodity->id,
                    'global_ranking' => rand(1, 100),
                    'demand_ranking' => rand(1, 100),
                    'supply_ranking' => rand(1, 100)
                ]);

                // Create Forecast
                \App\Models\CommodityForecast::create([
                    'commodity_id' => $commodity->id,
                    'forecast_date' => now()->addMonth()->format('Y-m-d'),
                    'predicted_price' => $basePrice + rand(-50, 50),
                    'predicted_demand' => rand(1000000, 9999999),
                    'predicted_supply' => rand(1000000, 9999999),
                    'confidence_level' => ['High', 'Medium', 'Low'][rand(0, 2)],
                ]);

                // Create Country Prices (For every country in the database)
                $countries = \App\Models\Country::all();
                foreach ($countries as $country) {
                    $countryPriceMultiplier = rand(80, 150) / 100; // 0.8x to 1.5x price difference
                    $countryBasePrice = $basePrice * $countryPriceMultiplier;

                    \App\Models\CommodityCountryPrice::create([
                        'commodity_id' => $commodity->id,
                        'country_id' => $country->id,
                        'selling_price' => $countryBasePrice + rand(10, 50),
                        'buying_price' => $countryBasePrice,
                        'import_cost' => $countryBasePrice * 0.05,
                        'export_cost' => $countryBasePrice * 0.04,
                        'shipping_cost' => rand(50, 200),
                        'estimated_profit' => rand(10, 50)
                    ]);
                }
            }
        }
    }
}
