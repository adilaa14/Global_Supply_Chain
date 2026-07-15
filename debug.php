<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'admin@globalsupply.com')->first();
if ($user) {
    echo "User exists. Password hash: " . $user->password . "\n";
    $match = \Illuminate\Support\Facades\Hash::check('password', $user->password) ? 'YES' : 'NO';
    echo "Does 'password' match? " . $match . "\n";
    
    // Force reset just in case
    $user->password = \Illuminate\Support\Facades\Hash::make('password');
    $user->save();
    echo "Password forced to 'password'\n";
} else {
    echo "User not found!\n";
}
