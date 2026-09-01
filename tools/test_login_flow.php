<?php
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap the Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$ic = '910101010101';
$password = 'password123';

// Update password
DB::table('users')->where('ic', $ic)->update(['password' => Hash::make($password), 'updated_at' => now()]);

$user = DB::table('users')->where('ic', $ic)->first();

if (!$user) {
    echo "User not found\n";
    exit(1);
}

$check = Hash::check($password, $user->password);

if ($check) {
    echo "Password reset successful. Login simulation for IC {$ic} with password '{$password}' succeeded.\n";
    echo "User: " . json_encode($user) . "\n";
    exit(0);
} else {
    echo "Password reset failed or hash mismatch.\n";
    exit(2);
}
