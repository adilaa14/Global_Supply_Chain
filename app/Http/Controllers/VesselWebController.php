<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\MapService;

class VesselWebController extends Controller
{
    protected MapService $mapService;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }

    public function index()
    {
        return Inertia::render('Tracking/GlobalMap');
    }

    public function list()
    {
        return Inertia::render('Tracking/VesselList');
    }

    public function show(string $id)
    {
        return Inertia::render('Tracking/VesselDetail', [
            'vesselId' => $id
        ]);
    }
}
