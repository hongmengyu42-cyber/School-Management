<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => [
                'required', 'exists:users,id',
                Rule::exists('users', 'id')->where('role', 'Teacher'),
            ],
        ];
    }
}
