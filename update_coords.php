<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$client = new \GuzzleHttp\Client(['verify' => false]);
try {
    $res = $client->get('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
    $data = json_decode($res->getBody(), true);
    
    $count = 0;
    foreach($data as $country) {
        if(isset($country['latlng'][0]) && isset($country['cca2'])) {
            $affected = \App\Models\Country::where('iso_code', $country['cca2'])->update([
                'latitude' => $country['latlng'][0],
                'longitude' => $country['latlng'][1]
            ]);
            if ($affected) {
                $count++;
            }
        }
    }
    echo "Successfully updated $count countries with real coordinates.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
