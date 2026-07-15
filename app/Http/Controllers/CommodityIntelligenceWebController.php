<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class CommodityIntelligenceWebController extends Controller
{
    public function index()
    {
        return Inertia::render('CommodityIntelligence/Index');
    }

    public function show($id)
    {
        return Inertia::render('CommodityIntelligence/Show', [
            'commodityId' => $id
        ]);
    }

    public function compare()
    {
        return Inertia::render('CommodityIntelligence/Compare');
    }
}
