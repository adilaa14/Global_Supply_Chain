<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryComparisonController extends Controller
{
    public function index(Request $request)
    {
        $ids = $request->input('ids');
        if (!$ids) {
            return response()->json([]);
        }

        $idArray = explode(',', $ids);
        if (count($idArray) > 5) {
            $idArray = array_slice($idArray, 0, 5);
        }

        $countries = Country::with(['economy', 'risk', 'opportunity', 'tradeStatistics', 'ranking'])
            ->whereIn('id', $idArray)
            ->get();

        return response()->json($countries);
    }
}
