<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function listAll()
    {
        return response()->json(Country::select('id', 'country_name', 'flag', 'iso_code')->orderBy('country_name')->get());
    }

    public function index(Request $request)
    {
        $query = Country::with(['economy', 'risk', 'opportunity', 'tradeStatistics', 'ranking']);
        
        if (auth('sanctum')->check()) {
            $query->withExists(['userFavorites as is_favorited' => function($q) {
                $q->where('user_id', auth('sanctum')->id());
            }]);
        }
        
        if ($request->has('region')) {
            $query->where('region', $request->region);
        }
        
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('country_name', 'like', '%' . $request->search . '%')
                  ->orWhere('iso_code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('sortBy')) {
            switch ($request->sortBy) {
                case 'risk_desc':
                    $query->orderBy('risk_score', 'desc');
                    break;
                case 'opp_desc':
                    $query->orderBy('opportunity_score', 'desc');
                    break;
                case 'gdp_desc':
                    $query->orderBy(
                        \App\Models\CountryEconomy::select('gdp')
                            ->whereColumn('country_economies.country_id', 'countries.id')
                            ->orderBy('year', 'desc')
                            ->limit(1),
                        'desc'
                    );
                    break;
                default:
                    $query->orderBy('country_name', 'asc');
                    break;
            }
        } else {
            $query->orderBy('country_name', 'asc');
        }

        return response()->json($query->paginate(20));
    }

    public function show($id, \App\Services\RiskScoringEngine $riskEngine)
    {
        $country = Country::with([
            'economy', 'risk', 'opportunity', 
            'tradeStatistics', 'tradeAgreements', 
            'regulations', 'ranking', 'ports'
        ])->findOrFail($id);

        $isoCode = $country->iso_code;

        $cacheKey = "country_live_data_{$isoCode}";
        $liveData = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function() use ($isoCode, $country) {
            $data = [
                'gdp' => 'N/A',
                'inflation' => 'N/A',
                'population' => 'N/A',
                'exports' => 'N/A',
                'imports' => 'N/A',
                'currency' => $country->currency_code ?? 'USD',
                'currency_name' => $country->currency_name ?? 'US Dollar',
                'region' => $country->region ?? 'N/A',
                'languages' => $country->language ?? 'N/A',
                'exchange_rate' => 'N/A'
            ];

            try {
                $responses = \Illuminate\Support\Facades\Http::pool(fn (\Illuminate\Http\Client\Pool $pool) => [
                    $pool->as('er')->withOptions(['verify' => false])->timeout(3)->get("https://open.er-api.com/v6/latest/USD"),
                    $pool->as('wb_gdp')->withOptions(['verify' => false])->timeout(3)->get("https://api.worldbank.org/v2/country/{$isoCode}/indicator/NY.GDP.MKTP.CD?format=json&per_page=5"),
                    $pool->as('wb_inf')->withOptions(['verify' => false])->timeout(3)->get("https://api.worldbank.org/v2/country/{$isoCode}/indicator/FP.CPI.TOTL.ZG?format=json&per_page=5"),
                    $pool->as('wb_pop')->withOptions(['verify' => false])->timeout(3)->get("https://api.worldbank.org/v2/country/{$isoCode}/indicator/SP.POP.TOTL?format=json&per_page=5"),
                    $pool->as('wb_exp')->withOptions(['verify' => false])->timeout(3)->get("https://api.worldbank.org/v2/country/{$isoCode}/indicator/NE.EXP.GNFS.CD?format=json&per_page=5"),
                    $pool->as('wb_imp')->withOptions(['verify' => false])->timeout(3)->get("https://api.worldbank.org/v2/country/{$isoCode}/indicator/NE.IMP.GNFS.CD?format=json&per_page=5"),
                ]);

                // 1. ExchangeRate
                if (isset($responses['er']) && $responses['er'] instanceof \Illuminate\Http\Client\Response && $responses['er']->successful()) {
                    $rates = $responses['er']->json()['rates'] ?? [];
                    if (isset($rates[$data['currency']])) {
                        $data['exchange_rate'] = '1 USD = ' . number_format($rates[$data['currency']], 2) . ' ' . $data['currency'];
                    } else {
                        $data['exchange_rate'] = '1 USD = 1 USD';
                    }
                }

                // 2. World Bank
                $parseWb = function($response) {
                    if (isset($response) && $response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                        $json = $response->json();
                        if (isset($json[1]) && is_array($json[1])) {
                            foreach ($json[1] as $item) {
                                if (isset($item['value']) && !is_null($item['value'])) {
                                    return $item['value'];
                                }
                            }
                        }
                    }
                    return null;
                };

                $gdp = $parseWb($responses['wb_gdp'] ?? null);
                if ($gdp) $data['gdp'] = '$' . number_format($gdp / 1000000000, 2) . ' Billion';

                $inf = $parseWb($responses['wb_inf'] ?? null);
                if ($inf) $data['inflation'] = number_format($inf, 2) . '%';

                $pop = $parseWb($responses['wb_pop'] ?? null);
                if ($pop) $data['population'] = number_format($pop) . ' people';

                $exp = $parseWb($responses['wb_exp'] ?? null);
                if ($exp) $data['exports'] = '$' . number_format($exp / 1000000000, 2) . ' Billion';

                $imp = $parseWb($responses['wb_imp'] ?? null);
                if ($imp) $data['imports'] = '$' . number_format($imp / 1000000000, 2) . ' Billion';

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('API Integration Failed: ' . $e->getMessage());
            }

            return $data;
        });

        // Inject Macroeconomic and Weather Data via RiskScoringEngine
        $macroData = $riskEngine->calculateCountryRisk($country);
        
        $country->setAttribute('macro_indicators', [
            'gdp' => $liveData['gdp'],
            'population' => $liveData['population'],
            'currency' => $liveData['currency'] . ' (' . $liveData['currency_name'] . ')',
            'region' => $liveData['region'],
            'languages' => $liveData['languages'],
            'exports' => $liveData['exports'],
            'imports' => $liveData['imports'],
            'exchange_rate' => $liveData['exchange_rate'],
            'inflation' => ['rate' => $liveData['inflation']],
            'weather' => $macroData['metrics']['weather'],
            'risk_level' => $macroData['risk_level'],
            'total_score' => $macroData['total_score'],
        ]);

        if (auth()->check()) {
            $country->setAttribute('is_favorited', \App\Models\UserFavorite::where('user_id', auth()->id())
                ->where('country_id', $country->id)->exists());
        }

        // Add Local Sentiment
        $recentNews = \App\Models\News::where('country_id', $country->id)->whereNotNull('sentiment')->latest('published_at')->take(10)->get();
        $sentimentStats = ['Positive' => 0, 'Neutral' => 0, 'Negative' => 0];
        foreach ($recentNews as $news) {
            if (isset($sentimentStats[$news->sentiment])) {
                $sentimentStats[$news->sentiment]++;
            }
        }
        $totalNews = count($recentNews);
        $country->setAttribute('local_sentiment', [
            'total_analyzed' => $totalNews,
            'positive_percent' => $totalNews > 0 ? round(($sentimentStats['Positive'] / $totalNews) * 100) : 0,
            'neutral_percent' => $totalNews > 0 ? round(($sentimentStats['Neutral'] / $totalNews) * 100) : 100,
            'negative_percent' => $totalNews > 0 ? round(($sentimentStats['Negative'] / $totalNews) * 100) : 0,
            'overall_status' => $totalNews == 0 ? 'No Data' : ($sentimentStats['Negative'] > $sentimentStats['Positive'] ? 'High Risk' : ($sentimentStats['Positive'] > $sentimentStats['Negative'] ? 'Favorable' : 'Neutral')),
            'recent_articles' => $recentNews
        ]);

        return response()->json($country);
    }

    public function toggleFavorite(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $exists = \App\Models\UserFavorite::where('user_id', $user->id)
            ->where('country_id', $id)->first();

        if ($exists) {
            $exists->delete();
            return response()->json(['status' => 'removed']);
        } else {
            \App\Models\UserFavorite::create([
                'user_id' => $user->id,
                'country_id' => $id
            ]);
            return response()->json(['status' => 'added']);
        }
    }

    public function favorites(Request $request)
    {
        $user = auth()->user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $countryIds = \App\Models\UserFavorite::where('user_id', $user->id)->pluck('country_id');
        
        $countries = Country::with(['economy', 'risk', 'opportunity', 'ranking'])
            ->whereIn('id', $countryIds)
            ->get();
            
        return response()->json($countries);
    }
}
