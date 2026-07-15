<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:sync-commodity-prices')]
#[Description('Command description')]
class SyncCommodityPrices extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(\App\Services\CommodityApiService $apiService)
    {
        $this->info('Starting Commodity Price Synchronization...');

        $commodities = \App\Models\Commodity::all();
        $updatedCount = 0;

        foreach ($commodities as $commodity) {
            $this->info("Fetching data for: {$commodity->commodity_name}");
            
            $marketData = $apiService->getRealTimePrice($commodity->commodity_name);

            if ($marketData) {
                // Determine trend
                $trend = 'Stable';
                if ($marketData['daily_change'] > 0.5) $trend = 'Up';
                if ($marketData['daily_change'] < -0.5) $trend = 'Down';

                // Update the latest price record (or create a new one for today)
                \App\Models\CommodityPrice::create([
                    'commodity_id' => $commodity->id,
                    'current_price' => $marketData['current_price'],
                    'open_price' => $marketData['current_price'] - $marketData['price_change'],
                    'close_price' => $marketData['current_price'],
                    'high' => $marketData['current_price'] * 1.01, // approximate
                    'low' => $marketData['current_price'] * 0.99,
                    'average' => $marketData['current_price'],
                    'moving_average' => $marketData['current_price'],
                    'price_change' => $marketData['price_change'],
                    'daily_change' => $marketData['daily_change'],
                    'weekly_change' => rand(-200, 200) / 100, // mock other periods for now
                    'monthly_change' => rand(-500, 500) / 100,
                    'yearly_change' => rand(-1000, 1000) / 100,
                    'volatility' => abs($marketData['daily_change']) * 2,
                    'trend' => $trend,
                    'last_calculated_at' => now()
                ]);

                // Also record in history
                \App\Models\CommodityPriceHistory::updateOrCreate(
                    ['commodity_id' => $commodity->id, 'date' => now()->format('Y-m-d')],
                    ['price' => $marketData['current_price']]
                );

                $this->info("Updated {$commodity->commodity_name}: $" . number_format($marketData['current_price'], 2) . " (" . $marketData['source'] . ")");
                $updatedCount++;
            } else {
                $this->warn("No API data found for {$commodity->commodity_name}");
            }
            
            // Sleep slightly to avoid API rate limits
            sleep(1);
        }

        $this->info("Synchronization Complete! Successfully updated {$updatedCount} commodities.");
    }
}
