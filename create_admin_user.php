<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('username', 'admin')->first();

if (! $user) {
    User::create([
        'name' => 'Admin User',
        'full_name' => 'Admin User',
        'username' => 'admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'role' => 'Admin',
        'status' => 'Active',
    ]);
}

echo 'ok';
