<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TradeOpportunity;
use App\Models\TradeSimulation;
use App\Models\TradeForecast;
use App\Models\TradeInsight;
use App\Models\TradeMarketAnalysis;
use App\Models\TradeRiskAnalysis;
use App\Models\AlternativeDestination;
use App\Models\Country;
use App\Models\Commodity;

class TradeIntelligenceController extends Controller
{
    public function dashboard(Request $request)
    {
        $topExports = TradeMarketAnalysis::with('country', 'commodity')
            ->orderBy('demand_score', 'desc')
            ->limit(5)
            ->get();
            
        $topImports = TradeMarketAnalysis::with('country', 'commodity')
            ->orderBy('supply_score', 'desc')
            ->limit(5)
            ->get();
            
        $bestMarket = TradeOpportunity::with('country', 'commodity')
            ->orderBy('opportunity_score', 'desc')
            ->first();
            
        $highestProfit = TradeOpportunity::with('country', 'commodity')
            ->orderBy('estimated_profit', 'desc')
            ->first();
            
        $lowestRisk = TradeRiskAnalysis::with('country')
            ->orderBy('total_risk_score', 'asc')
            ->first();

        $insights = TradeInsight::latest()->limit(5)->get();

        return response()->json([
            'summary' => [
                'total_opportunities' => TradeOpportunity::count(),
                'active_markets' => Country::count(),
                'monitored_commodities' => Commodity::count(),
                'avg_risk_score' => TradeRiskAnalysis::avg('total_risk_score') ?? 0,
            ],
            'top_exports' => $topExports,
            'top_imports' => $topImports,
            'best_market_today' => $bestMarket,
            'highest_profit_commodity' => $highestProfit,
            'lowest_risk_country' => $lowestRisk,
            'insights' => $insights
        ]);
    }

    public function opportunities(Request $request)
    {
        $query = TradeOpportunity::with('country', 'commodity');
        
        if ($request->has('country_id') && $request->country_id) {
            $query->where('country_id', $request->country_id);
        }
        if ($request->has('commodity_id') && $request->commodity_id) {
            $query->where('commodity_id', $request->commodity_id);
        }
        
        return response()->json($query->orderBy('opportunity_score', 'desc')->paginate(15));
    }

    public function risk(Request $request, \App\Services\RiskScoringEngine $riskEngine)
    {
        if ($request->has('country_id') && $request->country_id) {
            $country = Country::find($request->country_id);
            if ($country) {
                // Calculate and update on the fly
                $riskEngine->calculateCountryRisk($country);
            }
        } else {
            // Update top 5 countries dynamically for demo purposes
            $topCountries = Country::limit(5)->get();
            foreach($topCountries as $c) {
                $riskEngine->calculateCountryRisk($c);
            }
        }
        
        $query = TradeRiskAnalysis::with('country');
        
        if ($request->has('country_id') && $request->country_id) {
            $query->where('country_id', $request->country_id);
        }
        
        return response()->json($query->orderBy('total_risk_score', 'desc')->paginate(15));
    }

    public function forecast(Request $request)
    {
        $query = TradeForecast::with('country', 'commodity');
        
        if ($request->has('country_id') && $request->country_id) {
            $query->where('country_id', $request->country_id);
        }
        if ($request->has('commodity_id') && $request->commodity_id) {
            $query->where('commodity_id', $request->commodity_id);
        }
        
        return response()->json($query->orderBy('forecast_date', 'asc')->get());
    }

    public function insights(Request $request)
    {
        $insights = TradeInsight::orderBy('created_at', 'desc')->paginate(10);
        return response()->json($insights);
    }

    public function alternativeDestinations(Request $request)
    {
        $query = AlternativeDestination::with('originalDestination', 'alternativeCountry', 'commodity');
        
        if ($request->has('original_destination_id') && $request->original_destination_id) {
            $query->where('original_destination_id', $request->original_destination_id);
        }
        
        return response()->json($query->orderBy('profit_difference', 'desc')->paginate(10));
    }

    public function getSimulations(Request $request)
    {
        $query = TradeSimulation::with('originCountry', 'destinationCountry', 'commodity', 'user')
            ->orderBy('created_at', 'desc');
            
        return response()->json($query->paginate(10));
    }

    public function simulate(Request $request)
    {
        $validated = $request->validate([
            'commodity_id' => 'required|uuid|exists:commodities,id',
            'origin_country_id' => 'required|uuid|exists:countries,id',
            'destination_country_id' => 'required|uuid|exists:countries,id',
            'quantity' => 'required|numeric|min:1',
            'container_type' => 'nullable|string',
            'shipping_cost' => 'required|numeric|min:0',
            'insurance' => 'required|numeric|min:0',
            'import_tax' => 'required|numeric|min:0',
            'export_tax' => 'required|numeric|min:0',
            'currency' => 'nullable|string',
        ]);

        // Mock simulation logic
        $commodity = Commodity::with(['prices' => function($q) { $q->latest()->limit(1); }])->find($validated['commodity_id']);
        $unitPrice = $commodity->prices->first() ? $commodity->prices->first()->current_price : rand(500, 5000);
        
        $revenue = $unitPrice * $validated['quantity'];
        $cost = $validated['shipping_cost'] + $validated['insurance'] + $validated['import_tax'] + $validated['export_tax'] + ($unitPrice * $validated['quantity'] * 0.5); // Mock base cost
        $profit = $revenue - $cost;
        $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;
        $roi = $cost > 0 ? ($profit / $cost) * 100 : 0;
        $breakEven = $revenue / ($cost > 0 ? $cost : 1);

        $simulation = TradeSimulation::create(array_merge($validated, [
            'user_id' => $request->user() ? $request->user()->id : null,
            'revenue' => $revenue,
            'cost' => $cost,
            'profit' => $profit,
            'margin' => $margin,
            'roi' => $roi,
            'break_even_point' => $breakEven,
        ]));

        return response()->json([
            'message' => 'Simulation completed successfully',
            'data' => $simulation
        ]);
    }
}
