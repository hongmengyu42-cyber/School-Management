<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends AdminController
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::with('department', 'student')
                ->orderByDesc('id')
                ->paginate(25),
        ]);
    }

    public function create()
    {
        return view('admin.users.create', [
            'departments' => Department::orderBy('department_name')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = DB::transaction(function () use ($data) {
            $user = User::create($data);

            // Mirrors legacy behavior: creating a User with role=Student also
            // provisions the matching students row (student_number auto-generated).
            if ($user->role === 'Student') {
                Student::create([
                    'user_id' => $user->id,
                    'student_number' => 'STU-' . Str::padLeft((string) $user->id, 5, '0'),
                    'department_id' => $user->department_id,
                ]);
            }

            return $user;
        });

        $this->logActivity($request, 'user.created', "Created user {$user->username} ({$user->role})");

        return redirect()->route('admin.users.index')->with('status', 'User created.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user,
            'departments' => Department::orderBy('department_name')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        $this->logActivity($request, 'user.updated', "Updated user {$user->username}");

        return redirect()->route('admin.users.index')->with('status', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $username = $user->username;
        $user->delete();

        $this->logActivity(request(), 'user.deleted', "Deleted user {$username}");

        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }
}
