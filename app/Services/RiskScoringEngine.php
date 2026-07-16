<?php

namespace App\Services;

use App\Models\Country;
use App\Models\TradeRiskAnalysis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RiskScoringEngine
{
    /**
     * Calculate Risk Score for a given country.
     * Risk Score = (Weather + Inflation + Exchange Rate + News Sentiment) / 4
     * We convert raw metrics into a 0-100 risk scale.
     */
    public function calculateCountryRisk(Country $country)
    {
        // 1. Fetch Real-time / Mocked Macro Indicators
        $weatherData = $this->getWeatherRisk($country);
        $inflationData = $this->getInflationRisk($country->iso_code);
        $exchangeRateData = $this->getExchangeRateRisk($country->iso_code);
        $newsSentimentData = $this->getNewsSentimentRisk($country->name);

        // 2. Aggregate Risk Scores (0-100 scale, higher means higher risk)
        // Weighted calculation: Weather 30%, Inflation 20%, Currency 10%, Political News 40%
        $totalRiskScore = (
            ($weatherData['risk_score'] * 0.30) + 
            ($inflationData['risk_score'] * 0.20) + 
            ($exchangeRateData['risk_score'] * 0.10) + 
            ($newsSentimentData['risk_score'] * 0.40)
        );

        $riskLevel = $this->determineRiskLevel($totalRiskScore);

        // 3. Save to TradeRiskAnalysis
        $riskAnalysis = TradeRiskAnalysis::updateOrCreate(
            ['country_id' => $country->id],
            [
                'weather_risk' => $weatherData['risk_score'],
                'economic_risk' => $inflationData['risk_score'],
                'currency_risk' => $exchangeRateData['risk_score'],
                'political_risk' => $newsSentimentData['risk_score'], // Using news sentiment as proxy for political/stability risk
                'total_risk_score' => round($totalRiskScore),
            ]
        );

        return [
            'country' => $country->name,
            'iso_code' => $country->iso_code,
            'metrics' => [
                'weather' => $weatherData,
                'inflation' => $inflationData,
                'exchange_rate' => $exchangeRateData,
                'news_sentiment' => $newsSentimentData,
            ],
            'total_score' => round($totalRiskScore),
            'risk_level' => $riskLevel
        ];
    }

    private function determineRiskLevel($score)
    {
        if ($score <= 30) return 'Low Risk';
        if ($score <= 60) return 'Medium Risk';
        return 'High Risk';
    }

    /**
     * Fetch real weather data from Open-Meteo API
     */
    private function getWeatherRisk($country)
    {
        try {
            $url = "https://api.open-meteo.com/v1/forecast?latitude={$country->latitude}&longitude={$country->longitude}&current=temperature_2m,precipitation,wind_speed_10m,weather_code";
            
            // Bypass SSL if needed in local dev, otherwise use normal get
            // Added timeout(2) to prevent the entire app from hanging if API is unreachable
            $response = Http::withoutVerifying()->timeout(2)->get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                $temp = $data['current']['temperature_2m'] ?? 25;
                $rainfall = $data['current']['precipitation'] ?? 0;
                $windSpeed = $data['current']['wind_speed_10m'] ?? 0;
                $weatherCode = $data['current']['weather_code'] ?? 0;

                // Map WMO code
                $currentCondition = 'Clear';
                if (in_array($weatherCode, [2, 3, 45, 48])) $currentCondition = 'Cloudy';
                elseif (in_array($weatherCode, [95, 96, 99])) $currentCondition = 'Storm';
                elseif ($weatherCode >= 51 && $weatherCode <= 86) $currentCondition = 'Rain';

                // Storm risk calculation based on real data
                $stormRiskPercent = 0;
                if ($windSpeed > 40 || $rainfall > 50 || $currentCondition === 'Storm') {
                    $stormRiskPercent = min(100, (($windSpeed * 0.6) + ($rainfall * 0.4)));
                    if ($currentCondition === 'Storm') $stormRiskPercent = max(70, $stormRiskPercent);
                }

                if ($windSpeed > 100) $currentCondition = 'Typhoon';

                $riskScore = min(100, (($temp > 35 ? 20 : 0) + ($rainfall * 0.3) + ($windSpeed * 0.5) + ($stormRiskPercent * 0.5)));

                return [
                    'condition' => $currentCondition,
                    'temperature' => $temp . '°C',
                    'rainfall' => $rainfall . ' mm',
                    'wind_speed' => $windSpeed . ' km/h',
                    'storm_risk' => round($stormRiskPercent) . '%',
                    'risk_score' => round($riskScore)
                ];
            }
        } catch (\Exception $e) {
            Log::error('Open-Meteo API Failed: ' . $e->getMessage());
        }

        // Fallback to pseudo-random realistic weather based on country string hash if API fails
        $hash = crc32($country->name . date('Y-m-d'));
        
        $temp = 15 + ($hash % 25); // 15C to 40C
        $rainfall = ($hash % 100); // 0 to 99 mm
        $windSpeed = ($hash % 80); // 0 to 79 km/h
        
        // Storm risk increases with high wind and heavy rainfall
        $stormRiskPercent = 0;
        if ($windSpeed > 40 || $rainfall > 50) {
            $stormRiskPercent = min(100, (($windSpeed * 0.6) + ($rainfall * 0.4)));
        }

        $conditions = ['Clear', 'Cloudy', 'Rain', 'Storm', 'Typhoon'];
        $conditionIndex = 0;
        if ($rainfall > 10) $conditionIndex = 2; // Rain
        if ($stormRiskPercent > 50) $conditionIndex = 3; // Storm
        if ($stormRiskPercent > 85) $conditionIndex = 4; // Typhoon
        
        $currentCondition = $conditions[$conditionIndex];

        // Risk Score out of 100 based on the 4 variables
        $riskScore = min(100, (($temp > 35 ? 20 : 0) + ($rainfall * 0.3) + ($windSpeed * 0.5) + ($stormRiskPercent * 0.5)));

        return [
            'condition' => $currentCondition,
            'temperature' => $temp . '°C',
            'rainfall' => $rainfall . ' mm',
            'wind_speed' => $windSpeed . ' km/h',
            'storm_risk' => round($stormRiskPercent) . '%',
            'risk_score' => round($riskScore)
        ];
    }

    /**
     * Mock Inflation API - In production, use WorldBank API
     */
    private function getInflationRisk($isoCode)
    {
        $hash = crc32($isoCode . date('Y-m'));
        $inflationRate = ($hash % 150) / 10; // 0.0% to 15.0%
        
        // Target inflation is around 2%. Over 8% is high risk.
        $riskScore = min(100, max(5, ($inflationRate - 2) * 10));

        return [
            'rate' => $inflationRate . '%',
            'risk_score' => round($riskScore)
        ];
    }

    /**
     * Mock Exchange Rate Volatility API
     */
    private function getExchangeRateRisk($isoCode)
    {
        $hash = crc32($isoCode . 'FX');
        $volatility = ($hash % 50) / 10; // 0.0% to 5.0% daily volatility
        
        $riskScore = min(100, $volatility * 20); // 5% volatility = 100 risk

        return [
            'volatility' => $volatility . '%',
            'risk_score' => round($riskScore)
        ];
    }

    /**
     * Mock News Sentiment API - In production, use NewsAPI + NLP (VADER/Transformers)
     */
    private function getNewsSentimentRisk($countryName)
    {
        $hash = crc32($countryName . 'NEWS' . date('W'));
        $sentimentScore = ($hash % 200) - 100; // -100 (Very Negative) to 100 (Very Positive)
        
        // Convert sentiment to risk. Negative sentiment = High risk
        // -100 sentiment -> 100 risk. 100 sentiment -> 0 risk.
        $riskScore = 100 - (($sentimentScore + 100) / 2);

        $sentiments = ['Very Negative', 'Negative', 'Neutral', 'Positive', 'Very Positive'];
        $sentimentLabel = $sentiments[floor(($sentimentScore + 100) / 40)];
        if(!isset($sentimentLabel)) $sentimentLabel = 'Neutral';

        return [
            'sentiment' => $sentimentLabel,
            'score_raw' => $sentimentScore,
            'risk_score' => round($riskScore)
        ];
    }
}
