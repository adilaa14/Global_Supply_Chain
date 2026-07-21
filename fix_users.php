<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = \App\Models\Company::first();
if ($c) {
    $updated = \App\Models\User::whereNull('company_id')->update(['company_id' => $c->id]);
    echo "Assigned $updated users to company {$c->id}\n";
} else {
    echo "No companies found.\n";
}
