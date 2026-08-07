<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LinkParentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'parent_user_id' => [
                'required', 'exists:users,id',
                Rule::exists('users', 'id')->where('role', 'Parent'),
            ],
            'student_id' => ['required', 'exists:students,id'],
        ];
    }
}
