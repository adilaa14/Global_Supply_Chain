<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CommodityApiService
{
    /**
     * Fetch real market data for a specific commodity
     */
    public function getRealTimePrice($commodityName)
    {
        // Provider 1: Alpha Vantage (Free)
        $alphaVantageKey = config('services.alpha_vantage.key');
        
        // Map our commodity names to API symbols
        $symbols = [
            'Crude Oil' => 'WTI',
            'Natural Gas' => 'NATURAL_GAS',
            'Copper' => 'COPPER',
            'Aluminium' => 'ALUMINUM',
            'Wheat' => 'WHEAT',
            'Corn' => 'CORN',
            'Cotton' => 'COTTON',
            'Sugar' => 'SUGAR',
            'Coffee' => 'COFFEE',
            // Fallbacks or proxies if exact commodity isn't free
            'Gold' => 'GC=F', // Yahoo fallback
            'Silver' => 'SI=F',
        ];

        $symbol = $symbols[$commodityName] ?? null;

        if ($alphaVantageKey && $symbol && !str_contains($symbol, '=')) {
            return $this->fetchFromAlphaVantage($symbol, $alphaVantageKey);
        }

        // Provider 2: Yahoo Finance (Public/Open Endpoint - no key required)
        $yahooSymbols = [
            'Crude Oil' => 'CL=F',
            'Natural Gas' => 'NG=F',
            'Gold' => 'GC=F',
            'Silver' => 'SI=F',
            'Copper' => 'HG=F',
            'Wheat' => 'KE=F',
            'Corn' => 'ZC=F',
            'Soybean' => 'ZS=F',
            'Coffee' => 'KC=F',
            'Sugar' => 'SB=F',
            'Cotton' => 'CT=F',
            'Cocoa' => 'CC=F',
            'Steel' => 'HRC=F',
            'Nickel' => 'ALI=F',
            'Palm Oil' => 'CPO.KL' // Bursa Malaysia
        ];

        $ySymbol = $yahooSymbols[$commodityName] ?? null;
        if ($ySymbol) {
            return $this->fetchFromYahooFinance($ySymbol);
        }

        // If no API mapping found, return null
        return null;
    }

    private function fetchFromAlphaVantage($symbol, $key)
    {
        try {
            $url = "https://www.alphavantage.co/query?function={$symbol}&interval=monthly&apikey={$key}";
            $response = Http::get($url);
            
            if ($response->successful() && isset($response['data'][0]['value'])) {
                // Handle the case where the value might be '.'
                if ($response['data'][0]['value'] === '.') return null;

                $price = (float) $response['data'][0]['value'];
                // Get previous month to calculate change
                $prevPrice = isset($response['data'][1]['value']) && $response['data'][1]['value'] !== '.' 
                    ? (float) $response['data'][1]['value'] 
                    : $price;
                
                return [
                    'current_price' => $price,
                    'price_change' => $price - $prevPrice,
                    'daily_change' => $prevPrice > 0 ? (($price - $prevPrice) / $prevPrice) * 100 : 0,
                    'source' => 'Alpha Vantage'
                ];
            }
        } catch (\Exception $e) {
            Log::error("Alpha Vantage API Error: " . $e->getMessage());
        }
        return null;
    }

    private function fetchFromYahooFinance($symbol)
    {
        try {
            $url = "https://query2.finance.yahoo.com/v8/finance/chart/{$symbol}?interval=1d&range=2d";
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept' => 'application/json'
            ])->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['chart']['result'][0]['meta']['regularMarketPrice'])) {
                    $meta = $data['chart']['result'][0]['meta'];
                    $currentPrice = $meta['regularMarketPrice'];
                    $previousClose = $meta['previousClose'] ?? $currentPrice;
                    
                    $change = $currentPrice - $previousClose;
                    $percentChange = $previousClose > 0 ? ($change / $previousClose) * 100 : 0;

                    return [
                        'current_price' => $currentPrice,
                        'price_change' => $change,
                        'daily_change' => $percentChange,
                        'source' => 'Yahoo Finance Real-Time'
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error("Yahoo Finance API Error: " . $e->getMessage());
        }
        return null;
    }
}
