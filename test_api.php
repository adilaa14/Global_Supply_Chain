<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'admin@globalsupply.com')->first();
$service = app(App\Services\ShipmentService::class);
$shipments = $service->getAllShipments($user->company_id);

echo json_encode($shipments->toArray());
