<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Replaces auth/register.php. New accounts always land as Student /
     * Pending — Admin/Teacher accounts are never self-registered; they're
     * created via the Admin > Manage Users screen (Step 3.3), matching the
     * legacy system's behavior.
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        return User::create([
            'full_name' => $input['full_name'],
            'username' => $input['username'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => 'Student',
            'status' => 'Pending',
        ]);
    }
}
