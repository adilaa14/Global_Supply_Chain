<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$vessel = App\Models\Vessel::where('name', 'CMA CGM ANTOINE')->first();
$positions = App\Models\VesselPosition::where('vessel_id', $vessel->id)
    ->orderBy('timestamp', 'asc')
    ->get();

foreach($positions as $p) {
    echo $p->latitude . ',' . $p->longitude . "\n";
}
