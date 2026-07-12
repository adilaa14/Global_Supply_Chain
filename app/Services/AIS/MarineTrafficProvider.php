<?php

namespace App\Services\AIS;

use App\Contracts\AISProviderInterface;

class MarineTrafficProvider implements AISProviderInterface
{
    protected string $apiKey;
    protected string $baseUrl = 'https://services.marinetraffic.com/api';

    public function __construct()
    {
        $this->apiKey = config('services.marinetraffic.key', 'dummy_key');
    }

    public function getName(): string
    {
        return 'MarineTraffic';
    }

    public function getVesselPosition(string $imoOrMmsi): ?array
    {
        // Simulate realistic movement by slightly offsetting the last known position in DB
        $vessel = \App\Models\Vessel::where('imo_number', $imoOrMmsi)->orWhere('mmsi', $imoOrMmsi)->first();
        $lastPos = $vessel ? $vessel->latestPosition : null;

        if ($lastPos) {
            $lat = $lastPos->latitude;
            $lng = $lastPos->longitude;
            $heading = $lastPos->heading;

            $route = \App\Models\VesselRoute::where('vessel_id', $vessel->id)->where('is_active', true)->first();
            if ($route && $route->route_geometry) {
                $geometry = json_decode($route->route_geometry, true);
                if (count($geometry) > 1) {
                    // Find the next waypoint that is sufficiently far away (hasn't been reached yet)
                    // Find the absolute closest waypoint to current position
                    $closestIndex = 0;
                    $minDist = 999999;
                    foreach ($geometry as $index => $wp) {
                        $dist = sqrt(pow($wp[0] - $lat, 2) + pow($wp[1] - $lng, 2));
                        if ($dist < $minDist) {
                            $minDist = $dist;
                            $closestIndex = $index;
                        }
                    }

                    // Time-based smooth movement (20x Hyper Drive for Demo)
                    $timeDiffSeconds = time() - strtotime($lastPos->timestamp);
                    if ($timeDiffSeconds <= 0 || $timeDiffSeconds > 300) {
                        $timeDiffSeconds = 60; // fallback to 1 minute
                    }
                    
                    $speedKnots = $lastPos->speed ?? 20;
                    $degreesPerSecond = ($speedKnots / 100000) * 20; // Exactly matches frontend
                    $distanceToMove = $degreesPerSecond * $timeDiffSeconds;

                    // Walk the polyline to prevent overshooting
                    $currentLat = $lat;
                    $currentLng = $lng;
                    $currentIndex = $closestIndex;
                    $finalAngle = 0;

                    while ($distanceToMove > 0 && $currentIndex < count($geometry) - 1) {
                        $wp = $geometry[$currentIndex + 1];
                        $distToWp = sqrt(pow($wp[0] - $currentLat, 2) + pow($wp[1] - $currentLng, 2));
                        
                        $dLat = $wp[0] - $currentLat;
                        $dLng = $wp[1] - $currentLng;
                        $finalAngle = atan2($dLng, $dLat);

                        if ($distToWp <= $distanceToMove) {
                            $currentLat = $wp[0];
                            $currentLng = $wp[1];
                            $distanceToMove -= $distToWp;
                            $currentIndex++;
                        } else {
                            $currentLat += cos($finalAngle) * $distanceToMove;
                            $currentLng += sin($finalAngle) * $distanceToMove;
                            $distanceToMove = 0;
                        }
                    }

                    $lat = $currentLat;
                    $lng = $currentLng;
                    
                    $heading = $finalAngle * (180 / pi());
                    if ($heading < 0) $heading += 360;
                }
            } else {
                $lat += (rand(-10, 10) / 1000);
                $lng += (rand(-10, 10) / 1000);
            }
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
