<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $subjectId = $this->route('subject')->id;

        return [
            'subject_code' => ['required', 'string', 'max:100', "unique:subjects,subject_code,{$subjectId}"],
            'access_code' => ['nullable', 'string', 'max:100'],
            'subject_name' => ['required', 'string', 'max:255'],
            'teacher_id' => [
                'nullable', 'exists:users,id',
                Rule::exists('users', 'id')->where('role', 'Teacher'),
            ],
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'room_number' => ['nullable', 'string', 'max:100'],
            'days_of_week' => ['nullable', 'string', 'max:100'],
            'time_slot' => ['nullable', 'string', 'max:100'],
        ];
    }
}
