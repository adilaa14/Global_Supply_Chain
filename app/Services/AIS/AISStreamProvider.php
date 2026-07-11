<?php

namespace App\Services\AIS;

use App\Contracts\AISProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AISStreamProvider implements AISProviderInterface
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.aisstream.io/v1';

    public function __construct()
    {
        $this->apiKey = config('services.aisstream.key', 'dummy_key');
    }

    public function getName(): string
    {
        return 'AISStream';
    }

    public function getVesselPosition(string $imoOrMmsi): ?array
    {
        // Simulate realistic movement by slightly offsetting the last known position in DB
        $vessel = \App\Models\Vessel::where('imo_number', $imoOrMmsi)->orWhere('mmsi', $imoOrMmsi)->first();
        $lastPos = $vessel ? $vessel->latestPosition : null;

        if ($lastPos) {
            $lat = $lastPos->latitude + (rand(-10, 10) / 1000);
            $lng = $lastPos->longitude + (rand(-10, 10) / 1000);
            $heading = $lastPos->heading;
        } else {
            $lat = (float) rand(-4000, 4000) / 100;
            $lng = (float) rand(-10000, 10000) / 100;
            $heading = rand(0, 360);
        }

        return [
            'latitude' => $lat,
            'longitude' => $lng,
            'speed' => rand(15, 25),
            'heading' => $heading,
            'timestamp' => now(),
            'nav_status' => 'Under way using engine'
        ];
    }

    public function getFleetPositions(array $imoOrMmsiList): array
    {
        $positions = [];
        foreach ($imoOrMmsiList as $id) {
            $positions[$id] = $this->getVesselPosition($id);
        }
        return $positions;
    }

    public function getVesselDetails(string $imoOrMmsi): ?array
    {
        return [
            'vessel_type' => 'Cargo',
            'status' => 'Active'
        ];
    }
}
