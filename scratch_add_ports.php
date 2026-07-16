<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$in = \App\Models\Country::where('iso_code', 'IN')->first();
if ($in) {
    \App\Models\Port::updateOrCreate(
        ['port_code' => 'INBOM'],
        [
            'country_id' => $in->id,
            'port_name' => 'Port of Mumbai',
            'latitude' => 18.9438,
            'longitude' => 72.8358,
            'status' => 'active'
        ]
    );
    \App\Models\Port::updateOrCreate(
        ['port_code' => 'INMAA'],
        [
            'country_id' => $in->id,
            'port_name' => 'Port of Chennai',
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'status' => 'active'
        ]
    );
}

// Delete the INMCK mock port so it doesn't cause confusion
\App\Models\Port::where('port_code', 'LIKE', '%MCK')->delete();

echo "Real coastal ports for India added and mocks deleted.\n";
