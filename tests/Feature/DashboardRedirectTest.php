<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_dashboard_route_redirects_from_dashboard_path(): void
    {
        $user = User::create([
            'full_name' => 'Student User',
            'username' => 'student.user',
            'email' => 'student@example.com',
            'password' => bcrypt('password123'),
            'role' => 'Student',
            'status' => 'Active',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect('/student/dashboard');
    }
}
