<?php

namespace App\Services;

use App\Repositories\DashboardRepository;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    protected DashboardRepository $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getDashboardSummary(?string $companyId)
    {
        $cacheKey = "dashboard.summary.{$companyId}";
        
        return Cache::remember($cacheKey, 300, function () use ($companyId) {
            $snapshot = $this->dashboardRepository->getLatestSnapshot($companyId);
            $metrics = $this->dashboardRepository->getMetrics($companyId);
            
            $mappedMetrics = $metrics->mapWithKeys(function ($item) {
                return [$item->metric_key => $item->numeric_value !== null ? (int)$item->numeric_value : ($item->string_value ?? $item->json_value)];
            })->toArray();
            
            // Calculate Global Market Sentiment
            $recentNews = \App\Models\News::whereNotNull('sentiment')
                ->latest('published_at')
                ->take(50)
                ->get();
                
            $sentimentStats = ['Positive' => 0, 'Neutral' => 0, 'Negative' => 0];
            foreach ($recentNews as $news) {
                if (isset($sentimentStats[$news->sentiment])) {
                    $sentimentStats[$news->sentiment]++;
                }
            }
            
            $totalNews = count($recentNews);
            $marketSentiment = [
                'total_analyzed' => $totalNews,
                'positive_percent' => $totalNews > 0 ? round(($sentimentStats['Positive'] / $totalNews) * 100) : 0,
                'neutral_percent' => $totalNews > 0 ? round(($sentimentStats['Neutral'] / $totalNews) * 100) : 0,
                'negative_percent' => $totalNews > 0 ? round(($sentimentStats['Negative'] / $totalNews) * 100) : 0,
                'overall_status' => $sentimentStats['Negative'] > $sentimentStats['Positive'] ? 'Risk Warning' : 'Healthy',
            ];

            return [
                'snapshot' => $snapshot,
                'metrics' => $mappedMetrics,
                'market_sentiment' => $marketSentiment,
            ];
        });
    }

    public function clearCache(?string $companyId)
    {
        Cache::forget("dashboard.summary.{$companyId}");
    }
}
