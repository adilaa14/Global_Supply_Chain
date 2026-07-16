<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CountryIntelligenceWebController extends Controller
{
    public function index()
    {
        return Inertia::render('CountryIntelligence/Index');
    }

    public function show($id)
    {
        return Inertia::render('CountryIntelligence/Show', ['countryId' => $id]);
    }

    public function compare()
    {
        return Inertia::render('CountryIntelligence/Compare');
    }

    public function watchlist()
    {
        return Inertia::render('CountryIntelligence/Watchlist');
    }
}
