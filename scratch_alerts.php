<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = \Illuminate\Http\Request::create('/api/dashboard/alerts', 'GET', ['limit' => 5]);
$controller = app(\App\Http\Controllers\Api\GlobalAlertController::class);
$response = $controller->index($request);
echo "Response content:\n";
echo substr($response->getContent(), 0, 200) . "\n";
echo "Type of data: " . gettype(json_decode($response->getContent(), true)['data']) . "\n";
