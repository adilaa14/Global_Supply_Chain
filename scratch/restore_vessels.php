<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vessel;

$vessels = [
    [
        'name' => 'MSC GULSUN',
        'imo_number' => '9839430',
        'mmsi' => '351888000',
        'vessel_type' => 'Container',
        'build_year' => 2019,
        'status' => 'Active',
    ],
    [
        'name' => 'CMA CGM ANTOINE',
        'imo_number' => '9776418',
        'mmsi' => '228334000',
        'vessel_type' => 'Container',
        'build_year' => 2018,
        'status' => 'Active',
    ],
    [
        'name' => 'HMM ALGECIRAS',
        'imo_number' => '9863297',
        'mmsi' => '440338000',
        'vessel_type' => 'Cargo',
        'build_year' => 2020,
        'status' => 'Active',
    ]
];

foreach ($vessels as $data) {
    if (!Vessel::where('name', $data['name'])->exists()) {
        Vessel::create($data);
        echo "Created " . $data['name'] . "\n";
    }
}
echo "Done.\n";
