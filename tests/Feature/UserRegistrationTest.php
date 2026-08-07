<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_creation_backfills_name_from_full_name(): void
    {
        $user = User::create([
            'full_name' => 'Rathana',
            'username' => 's.rathana',
            'email' => 'rathana@example.com',
            'password' => Hash::make('password123'),
            'role' => 'Student',
            'status' => 'Pending',
        ]);

        $this->assertSame('Rathana', $user->fresh()->name);
    }
}
