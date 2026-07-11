<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vessel;
use App\Models\VesselPosition;

class VesselSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data to avoid duplicates
        \App\Models\VesselRoute::truncate();
        VesselPosition::truncate();
        Vessel::truncate();
        
        // Define realistic global ports
        $portData = [
            ['name' => 'Port of Singapore', 'code' => 'SGSIN', 'country' => 'Singapore', 'lat' => 1.264, 'lng' => 103.840],
            ['name' => 'Port of Rotterdam', 'code' => 'NLRTM', 'country' => 'Netherlands', 'lat' => 51.8850, 'lng' => 4.2867],
            ['name' => 'Port of Los Angeles', 'code' => 'USLAX', 'country' => 'United States', 'lat' => 33.7288, 'lng' => -118.2620],
            ['name' => 'Port of Shanghai', 'code' => 'CNSHA', 'country' => 'China', 'lat' => 31.2222, 'lng' => 121.4581],
            ['name' => 'Port of Dubai', 'code' => 'AEJEA', 'country' => 'UAE', 'lat' => 25.0112, 'lng' => 55.0556],
            ['name' => 'Port of Cape Town', 'code' => 'ZACPT', 'country' => 'South Africa', 'lat' => -33.900, 'lng' => 18.433],
            ['name' => 'Port of Sydney', 'code' => 'AUSYD', 'country' => 'Australia', 'lat' => -33.8688, 'lng' => 151.2093],
        ];

        $ports = [];
        foreach ($portData as $pd) {
            $country = \App\Models\Country::firstOrCreate(['iso_code' => strtoupper(substr($pd['country'], 0, 3))], ['country_name' => $pd['country']]);
            $ports[] = \App\Models\Port::updateOrCreate(
                ['port_code' => $pd['code']],
                ['country_id' => $country->id, 'port_name' => $pd['name'], 'latitude' => $pd['lat'], 'longitude' => $pd['lng']]
            );
        }

        $vessels = [
            [
                'name' => 'EVER GIVEN',
                'imo_number' => '9811000',
                'mmsi' => '353136000',
                'vessel_type' => 'Container',
                'build_year' => 2018,
                'status' => 'Active',
                'start_port' => 0, // Singapore
                'dest_port' => 1,  // Rotterdam
            ],
            [
                'name' => 'MSC GULSUN',
                'imo_number' => '9839430',
                'mmsi' => '351888000',
                'vessel_type' => 'Container',
                'build_year' => 2019,
                'status' => 'Active',
                'start_port' => 3, // Shanghai
                'dest_port' => 2,  // LA
            ],
            [
                'name' => 'CMA CGM ANTOINE',
                'imo_number' => '9776418',
                'mmsi' => '228334000',
                'vessel_type' => 'Container',
                'build_year' => 2018,
                'status' => 'Active',
                'start_port' => 1, // Rotterdam
                'dest_port' => 4,  // Dubai
            ],
            [
                'name' => 'HMM ALGECIRAS',
                'imo_number' => '9863297',
                'mmsi' => '440338000',
                'vessel_type' => 'Cargo',
                'build_year' => 2020,
                'status' => 'Active',
                'start_port' => 2, // Los Angeles
                'dest_port' => 3,  // Shanghai
            ]
        ];

        foreach ($vessels as $data) {
            $startPortIndex = $data['start_port'];
            $destPortIndex = $data['dest_port'];
            unset($data['start_port'], $data['dest_port']);
            
            $vessel = Vessel::create($data);

            \App\Models\VesselRoute::create([
                'vessel_id' => $vessel->id,
                'origin_port_id' => $ports[$startPortIndex]->id,
                'destination_port_id' => $ports[$destPortIndex]->id,
                'departure_time' => now()->subDays(5),
                'estimated_arrival' => now()->addDays(15),
                'is_active' => true,
            ]);

            // Starting coordinates exactly at the port
            $lat = $ports[$startPortIndex]->latitude;
            $lng = $ports[$startPortIndex]->longitude;
            $heading = rand(0, 360);

            // Generate history going BACKWARDS in time, moving AWAY from the port into the ocean
            // Since we are moving backwards, we ADD dLat and dLng to go into the ocean.
            $dLat = 0; $dLng = 0;
            if ($startPortIndex == 0) { $dLat = -0.02; $dLng = 0.05; } // Singapore -> Ocean is South East
            if ($startPortIndex == 1) { $dLat = 0.03; $dLng = -0.04; } // Rotterdam -> Ocean is North West
            if ($startPortIndex == 2) { $dLat = -0.03; $dLng = -0.05; } // LA -> Ocean is South West
            if ($startPortIndex == 3) { $dLat = -0.03; $dLng = 0.05; } // Shanghai -> Ocean is South East

            // Current position is the port
            $currLat = $lat;
            $currLng = $lng;

            for ($i = 0; $i <= 30; $i++) {
                VesselPosition::create([
                    'vessel_id' => $vessel->id,
                    'latitude' => $currLat,
                    'longitude' => $currLng,
                    'speed' => rand(15, 20),
                    'heading' => $heading,
                    'course' => $heading,
                    'nav_status' => $i === 0 ? 'Moored' : 'Under way using engine',
                    'timestamp' => now()->subMinutes($i * 60), // Past 30 hours
                    'ais_provider' => 'MarineTraffic'
                ]);

                // Move ship backwards towards ocean
                $currLat += $dLat;
                $currLng += $dLng;
                
                // Add some slight variation so it's not perfectly straight
                $currLat += (rand(-2, 2) / 1000);
                $currLng += (rand(-2, 2) / 1000);
            }
        }
    }
}
