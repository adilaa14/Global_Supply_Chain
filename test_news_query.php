<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$category = 'Logistics';
$isoCode = 'AR';
$localQuery = \App\Models\News::query();

$localQuery->whereHas('country', function($q) use ($isoCode) {
    $q->where('iso_code', $isoCode);
});

$localQuery->where(function($q) use ($category) {
    $q->where('category', 'like', "%{$category}%")
      ->orWhere('title', 'like', "%{$category}%");
});

echo json_encode($localQuery->get()->toArray(), JSON_PRETTY_PRINT);
