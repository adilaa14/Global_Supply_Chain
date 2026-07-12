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

            // Define highly accurate route geometry to avoid any landmass clipping (especially Rotterdam & Shanghai)
            $routeGeometry = [];
            if ($startPortIndex == 0 && $destPortIndex == 1) {
                // Singapore to Rotterdam
                $routeGeometry = [
                    [$ports[0]->latitude, $ports[0]->longitude],
                    [1.250, 103.800], [1.220, 103.750], [1.180, 103.650], [1.150, 103.400], // Singapore coast
                    [1.3, 103.0], [2.0, 102.0], [5.5, 98.0], // Strait of Malacca
                    [5.8, 80.0], [12.0, 60.0], [12.5, 45.0], [12.5, 43.5], // South of Sri Lanka -> Gulf of Aden -> Bab el-Mandeb
                    [20.0, 38.0], [28.0, 33.5], [29.9, 32.5], [30.6, 32.3], [31.2, 32.3], // Red Sea -> Suez Canal
                    [31.5, 32.0], [33.0, 25.0], [35.0, 15.0], [37.5, 4.0], [36.5, -1.0], // Mediterranean Sea
                    [35.9, -5.5], [35.9, -6.5], [36.9, -9.0], [38.5, -10.0], [43.0, -10.0], // Gibraltar -> Around Spain/Portugal
                    [45.0, -8.0], [49.5, -4.0], [51.1, 1.5], // Bay of Biscay -> English Channel -> Dover Strait
                    [51.95, 3.30], [51.99, 3.90], [51.98, 4.05], [51.97, 4.10], [51.95, 4.14], [51.93, 4.18], [51.905, 4.24], [51.895, 4.26], // Nieuwe Waterweg river
                    [$ports[1]->latitude, $ports[1]->longitude]
                ];
            } elseif ($startPortIndex == 3 && $destPortIndex == 2) {
                // Shanghai (Huangpu River) to LA
                $routeGeometry = [
                    [$ports[3]->latitude, $ports[3]->longitude],
                    [31.250, 121.500], [31.280, 121.530], [31.330, 121.500], [31.380, 121.500], // Huangpu River bends
                    [31.400, 121.550], [31.350, 121.700], [31.200, 121.900], [31.100, 122.100], [30.800, 122.500], // Yangtze to open sea
                    [30.0, 125.0], [30.5, 131.0], [33.0, 137.0], [35.0, 142.0], // South of Japan (avoiding Honshu)
                    [40.0, 160.0], [42.0, 190.0], [40.0, 220.0], [33.5, 241.0], [33.60, 241.7], [33.70, 241.75], // Pacific to LA Breakwater
                    [$ports[2]->latitude, 360 + $ports[2]->longitude] 
                ];
            } elseif ($startPortIndex == 1 && $destPortIndex == 4) {
                // Rotterdam (Nieuwe Waterweg) to Dubai
                $routeGeometry = [
                    [$ports[1]->latitude, $ports[1]->longitude],
                    [51.895, 4.26], [51.905, 4.24], [51.93, 4.18], [51.95, 4.14], [51.97, 4.10], [51.98, 4.05], [51.99, 3.90], [51.95, 3.30], // Exit River
                    [51.1, 1.5], [49.5, -4.0], [45.0, -8.0], [43.0, -10.0], [38.5, -10.0], [36.9, -9.0], [35.9, -6.5], [35.9, -5.5], // Dover -> English Channel -> Biscay -> Around Portugal/Spain -> Gibraltar
                    [36.5, -1.0], [37.5, 4.0], [35.0, 15.0], [33.0, 25.0], [31.5, 32.0], // Mediterranean
                    [31.2, 32.3], [30.6, 32.3], [29.9, 32.5], [28.0, 33.5], [20.0, 38.0], [12.5, 43.5], // Suez -> Red Sea -> Bab el-Mandeb
                    [12.5, 45.0], [14.0, 50.0], [15.0, 55.0], [22.0, 60.0], // Gulf of Aden -> Arabian Sea
                    [24.5, 59.0], [26.3, 56.5], [26.0, 55.8], [25.5, 55.2], // Gulf of Oman -> Strait of Hormuz -> Persian Gulf
                    [$ports[4]->latitude, $ports[4]->longitude]
                ];
            } elseif ($startPortIndex == 2 && $destPortIndex == 3) {
                // LA to Shanghai (Negative longitudes)
                $routeGeometry = [
                    [$ports[2]->latitude, $ports[2]->longitude],
                    [33.700, -118.250], [33.650, -118.220], [33.600, -118.300], [33.500, -118.500], [33.0, -120.0], // LA exit
                    [34.0, -125.0], [40.0, -140.0], [42.0, -170.0], [40.0, -200.0],
                    [35.0, -218.0], [33.0, -223.0], [30.5, -229.0], [30.0, -235.0], // South of Japan approaching China
                    [30.8, -237.5], [31.1, -237.9], [31.2, -238.1], [31.35, -238.3], [31.40, -238.45], // Enter Yangtze
                    [31.380, -238.50], [31.330, -238.50], [31.280, -238.47], [31.250, -238.50], // Huangpu River inward
                    [$ports[3]->latitude, $ports[3]->longitude - 360]
                ];
            }

            \App\Models\VesselRoute::create([
                'vessel_id' => $vessel->id,
                'origin_port_id' => $ports[$startPortIndex]->id,
                'destination_port_id' => $ports[$destPortIndex]->id,
                'departure_time' => now()->subDays(5),
                'estimated_arrival' => now()->addDays(15),
                'route_geometry' => json_encode($routeGeometry),
                'is_active' => true,
            ]);

            // Starting coordinates exactly at the port
            $lat = $ports[$startPortIndex]->latitude;
            $lng = $ports[$startPortIndex]->longitude;
            $heading = rand(0, 360);

            // Create only the initial departing position
            // The history will naturally grow as the tracking service polls and the ship moves
            VesselPosition::create([
                'vessel_id' => $vessel->id,
                'latitude' => $lat,
                'longitude' => $lng,
                'speed' => rand(15, 20),
                'heading' => $heading,
                'course' => $heading,
                'nav_status' => 'Moored',
                'timestamp' => now()->subMinutes(1), 
                'ais_provider' => 'MarineTraffic'
            ]);
        }
    }
}
