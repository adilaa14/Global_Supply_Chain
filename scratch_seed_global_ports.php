<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ports = [
    // East Asia
    ['code' => 'JPTYO', 'name' => 'Port of Tokyo', 'lat' => 35.61, 'lng' => 139.79, 'country' => 'Japan'],
    ['code' => 'KRPUS', 'name' => 'Port of Busan', 'lat' => 35.10, 'lng' => 129.04, 'country' => 'South Korea'],
    ['code' => 'TWTPE', 'name' => 'Port of Taipei', 'lat' => 25.15, 'lng' => 121.38, 'country' => 'Taiwan'],
    ['code' => 'HKHKG', 'name' => 'Port of Hong Kong', 'lat' => 22.33, 'lng' => 114.13, 'country' => 'Hong Kong'],
    
    // Southeast Asia
    ['code' => 'PHMNL', 'name' => 'Port of Manila', 'lat' => 14.59, 'lng' => 120.96, 'country' => 'Philippines'],
    ['code' => 'VNSGN', 'name' => 'Port of Ho Chi Minh', 'lat' => 10.76, 'lng' => 106.74, 'country' => 'Vietnam'],
    ['code' => 'THLCH', 'name' => 'Port of Laem Chabang', 'lat' => 13.08, 'lng' => 100.88, 'country' => 'Thailand'],
    ['code' => 'IDJKT', 'name' => 'Port of Tanjung Priok (Jakarta)', 'lat' => -6.10, 'lng' => 106.88, 'country' => 'Indonesia'],
    ['code' => 'IDTPE', 'name' => 'Port of Tanjung Perak (Surabaya)', 'lat' => -7.20, 'lng' => 112.73, 'country' => 'Indonesia'],
    ['code' => 'MYPKG', 'name' => 'Port Klang', 'lat' => 3.00, 'lng' => 101.39, 'country' => 'Malaysia'],

    // South Asia & Middle East
    ['code' => 'BGCGP', 'name' => 'Port of Chittagong', 'lat' => 22.28, 'lng' => 91.79, 'country' => 'Bangladesh'],
    ['code' => 'LKMBA', 'name' => 'Port of Colombo', 'lat' => 6.94, 'lng' => 79.84, 'country' => 'Sri Lanka'],
    ['code' => 'PKKHI', 'name' => 'Port of Karachi', 'lat' => 24.81, 'lng' => 66.97, 'country' => 'Pakistan'],
    ['code' => 'OMSAA', 'name' => 'Port of Salalah', 'lat' => 16.94, 'lng' => 54.00, 'country' => 'Oman'],
    ['code' => 'SAJED', 'name' => 'Port of Jeddah', 'lat' => 21.48, 'lng' => 39.16, 'country' => 'Saudi Arabia'],
    
    // Europe
    ['code' => 'EGPSD', 'name' => 'Port Said', 'lat' => 31.26, 'lng' => 32.31, 'country' => 'Egypt'],
    ['code' => 'ITGOA', 'name' => 'Port of Genoa', 'lat' => 44.40, 'lng' => 8.91, 'country' => 'Italy'],
    ['code' => 'ESBCN', 'name' => 'Port of Barcelona', 'lat' => 41.34, 'lng' => 2.16, 'country' => 'Spain'],
    ['code' => 'FRMRS', 'name' => 'Port of Marseille', 'lat' => 43.32, 'lng' => 5.35, 'country' => 'France'],
    ['code' => 'GBFEL', 'name' => 'Port of Felixstowe', 'lat' => 51.95, 'lng' => 1.31, 'country' => 'United Kingdom'],
    ['code' => 'DEHAM', 'name' => 'Port of Hamburg', 'lat' => 53.53, 'lng' => 9.96, 'country' => 'Germany'],
    ['code' => 'BEANR', 'name' => 'Port of Antwerp', 'lat' => 51.27, 'lng' => 4.35, 'country' => 'Belgium'],

    // Americas
    ['code' => 'USNYC', 'name' => 'Port of New York', 'lat' => 40.67, 'lng' => -74.04, 'country' => 'United States'],
    ['code' => 'USMIA', 'name' => 'Port of Miami', 'lat' => 25.77, 'lng' => -80.17, 'country' => 'United States'],
    ['code' => 'USSEA', 'name' => 'Port of Seattle', 'lat' => 47.61, 'lng' => -122.35, 'country' => 'United States'],
    ['code' => 'CAVAN', 'name' => 'Port of Vancouver', 'lat' => 49.28, 'lng' => -123.11, 'country' => 'Canada'],
    ['code' => 'MXZLO', 'name' => 'Port of Manzanillo', 'lat' => 19.06, 'lng' => -104.30, 'country' => 'Mexico'],
    ['code' => 'BRSSZ', 'name' => 'Port of Santos', 'lat' => -23.97, 'lng' => -46.30, 'country' => 'Brazil'],
    ['code' => 'ARBUE', 'name' => 'Port of Buenos Aires', 'lat' => -34.58, 'lng' => -58.37, 'country' => 'Argentina'],
    ['code' => 'PECLO', 'name' => 'Port of Callao', 'lat' => -12.05, 'lng' => -77.14, 'country' => 'Peru'],
    ['code' => 'CLVAP', 'name' => 'Port of Valparaiso', 'lat' => -33.03, 'lng' => -71.62, 'country' => 'Chile'],

    // Africa & Oceania
    ['code' => 'KEMBA', 'name' => 'Port of Mombasa', 'lat' => -4.06, 'lng' => 39.65, 'country' => 'Kenya'],
    ['code' => 'TZDAR', 'name' => 'Port of Dar es Salaam', 'lat' => -6.82, 'lng' => 39.29, 'country' => 'Tanzania'],
    ['code' => 'NZAKL', 'name' => 'Port of Auckland', 'lat' => -36.84, 'lng' => 174.77, 'country' => 'New Zealand'],
    ['code' => 'AUMEL', 'name' => 'Port of Melbourne', 'lat' => -37.82, 'lng' => 144.91, 'country' => 'Australia'],
];

foreach ($ports as $p) {
    // Find or create country
    $country = \App\Models\Country::firstOrCreate(
        ['country_name' => $p['country']],
        ['iso_code' => strtoupper(substr($p['country'], 0, 2))]
    );

    \App\Models\Port::updateOrCreate(
        ['port_code' => $p['code']],
        [
            'port_name' => $p['name'],
            'country_id' => $country->id,
            'latitude' => $p['lat'],
            'longitude' => $p['lng']
        ]
    );
}

echo "Successfully seeded " . count($ports) . " major global ports!\n";
