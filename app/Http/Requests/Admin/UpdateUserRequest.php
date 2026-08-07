<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', "unique:users,username,{$userId}"],
            'email' => ['required', 'string', 'email', 'max:255', "unique:users,email,{$userId}"],
            // Password is optional on edit — legacy behavior: blank means "don't change it".
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'in:Admin,Teacher,Student,Parent'],
            'status' => ['required', 'in:Pending,Active,Suspended'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ];
    }
}
