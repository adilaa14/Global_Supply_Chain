<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = \App\Models\Country::first();
$response = app(\App\Http\Controllers\Api\CountryController::class)->show($c->id, app(\App\Services\RiskScoringEngine::class));

$data = json_decode($response->getContent(), true);
print_r($data['local_sentiment']);
