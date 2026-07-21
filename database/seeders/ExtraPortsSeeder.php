<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Port;
use App\Models\Country;

class ExtraPortsSeeder extends Seeder
{
    public function run(): void
    {
        $portData = [
            ['name' => 'Port of Antwerp', 'code' => 'BEANR', 'country' => 'Belgium', 'iso' => 'BE', 'lat' => 51.2194, 'lng' => 4.4025],
            ['name' => 'Port of Hamburg', 'code' => 'DEHAM', 'country' => 'Germany', 'iso' => 'DE', 'lat' => 53.5511, 'lng' => 9.9937],
            ['name' => 'Port of Busan', 'code' => 'KRPUS', 'country' => 'South Korea', 'iso' => 'KR', 'lat' => 35.1796, 'lng' => 129.0756],
            ['name' => 'Port of Hong Kong', 'code' => 'HKHKG', 'country' => 'Hong Kong', 'iso' => 'HK', 'lat' => 22.3193, 'lng' => 114.1694],
            ['name' => 'Port of New York / New Jersey', 'code' => 'USNYC', 'country' => 'United States', 'iso' => 'US', 'lat' => 40.7128, 'lng' => -74.0060],
            ['name' => 'Port of Santos', 'code' => 'BRSSZ', 'country' => 'Brazil', 'iso' => 'BR', 'lat' => -23.9618, 'lng' => -46.3322],
            ['name' => 'Port of Mumbai', 'code' => 'INBOM', 'country' => 'India', 'iso' => 'IN', 'lat' => 18.9220, 'lng' => 72.8347],
            ['name' => 'Port of Tokyo', 'code' => 'JPTYO', 'country' => 'Japan', 'iso' => 'JP', 'lat' => 35.6895, 'lng' => 139.6917],
            ['name' => 'Port of Vancouver', 'code' => 'CAVAN', 'country' => 'Canada', 'iso' => 'CA', 'lat' => 49.2827, 'lng' => -123.1207],
            ['name' => 'Port of Felixstowe', 'code' => 'GBFXT', 'country' => 'United Kingdom', 'iso' => 'GB', 'lat' => 51.9611, 'lng' => 1.3503],
            ['name' => 'Port of Melbourne', 'code' => 'AUMEL', 'country' => 'Australia', 'iso' => 'AU', 'lat' => -37.8136, 'lng' => 144.9631],
            ['name' => 'Port of Tanjung Priok', 'code' => 'IDTPP', 'country' => 'Indonesia', 'iso' => 'ID', 'lat' => -6.1037, 'lng' => 106.8833],
        ];

        foreach ($portData as $pd) {
            $country = Country::where('iso_code', $pd['iso'])->first();
            if (!$country) {
                $country = Country::create(['iso_code' => $pd['iso'], 'country_name' => $pd['country']]);
            }
            Port::updateOrCreate(
                ['port_code' => $pd['code']],
                ['country_id' => $country->id, 'port_name' => $pd['name'], 'latitude' => $pd['lat'], 'longitude' => $pd['lng']]
            );
        }
    }
}
