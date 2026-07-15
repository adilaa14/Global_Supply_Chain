<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use App\Models\CommodityCategory;
use Illuminate\Http\Request;

class CommodityController extends Controller
{
    public function index(Request $request)
    {
        $query = Commodity::with([
            'category', 
            'prices' => function($q) { $q->orderBy('created_at', 'desc')->limit(1); },
            'demands' => function($q) { $q->orderBy('year', 'desc')->limit(1); },
            'supplies' => function($q) { $q->orderBy('year', 'desc')->limit(1); },
            'ranking',
            'market'
        ]);

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('commodity_name', 'like', '%' . $request->search . '%')
                  ->orWhere('commodity_code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('category') && $request->category !== 'All') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        return response()->json($query->paginate(20));
    }

    public function show($id)
    {
        $commodity = Commodity::with([
            'category', 
            'prices' => function($q) { $q->orderBy('created_at', 'desc')->limit(1); },
            'demands' => function($q) { $q->orderBy('year', 'desc')->limit(1); },
            'supplies' => function($q) { $q->orderBy('year', 'desc')->limit(1); },
            'market',
            'forecasts' => function($q) { $q->orderBy('forecast_date', 'asc'); },
            'countryPrices.country'
        ])->findOrFail($id);

        return response()->json($commodity);
    }

    public function history($id, Request $request)
    {
        $days = $request->input('days', 30);
        $history = \App\Models\CommodityPriceHistory::where('commodity_id', $id)
            ->where('date', '>=', now()->subDays($days))
            ->orderBy('date', 'asc')
            ->get();
        return response()->json($history);
    }

    public function comparison(Request $request)
    {
        $ids = explode(',', $request->input('ids', ''));
        if (empty($ids) || $ids[0] === '') return response()->json([]);

        $commodities = Commodity::with([
            'category',
            'prices' => function($q) { $q->orderBy('created_at', 'desc')->limit(1); },
            'demands' => function($q) { $q->orderBy('year', 'desc')->limit(1); },
            'supplies' => function($q) { $q->orderBy('year', 'desc')->limit(1); },
            'market'
        ])->whereIn('id', $ids)->get();

        return response()->json($commodities);
    }

    public function categories()
    {
        return response()->json(CommodityCategory::orderBy('name')->get());
    }

    public function listAll()
    {
        return response()->json(Commodity::select('id', 'commodity_name', 'commodity_code')->orderBy('commodity_name')->get());
    }
}
