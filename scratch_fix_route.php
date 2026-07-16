<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$port = \App\Models\Port::where('port_code', 'INBOM')->first();
\App\Models\VesselRoute::where('is_active', true)->update(['destination_port_id' => $port->id, 'route_geometry' => null]);
echo "Updated active routes to Port of Mumbai.\n";
