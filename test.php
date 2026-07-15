<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $r = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])->get('https://api.frankfurter.app/2026-01-01..2026-07-16?from=USD&to=IDR');
    echo $r->status() . PHP_EOL;
    echo substr($r->body(), 0, 200);
} catch(\Exception $e) {
    echo $e->getMessage();
}
