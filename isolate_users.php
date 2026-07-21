<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$adminCompany = \App\Models\Company::first();

// Find all users who were mistakenly assigned to the admin company
// (Except the actual admin users who were supposed to be there)
$users = \App\Models\User::where('company_id', $adminCompany->id)
    ->where('email', '!=', 'admin@globalsupply.com')
    ->get();

foreach ($users as $user) {
    // Create a new isolated company for them
    $company = \App\Models\Company::create([
        'company_name' => $user->name . ' Enterprise',
        'company_type' => 'Both',
        'email' => $user->email,
        'status' => 'active',
    ]);
    
    $user->update(['company_id' => $company->id]);
    echo "Isolated user {$user->email} to new company {$company->company_name}\n";
}

echo "Done.\n";
