<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function listAll()
    {
        return response()->json(Country::select('id', 'country_name', 'flag')->orderBy('country_name')->get());
    }

    public function index(Request $request)
    {
        $query = Country::with(['economy', 'risk', 'opportunity', 'tradeStatistics', 'ranking']);
        
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

    public function show($id)
    {
        $country = Country::with([
            'economy', 'risk', 'opportunity', 
            'tradeStatistics', 'tradeAgreements', 
            'regulations', 'ranking', 'ports'
        ])->findOrFail($id);

        return response()->json($country);
    }
}
