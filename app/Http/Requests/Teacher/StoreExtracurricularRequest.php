<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreExtracurricularRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('subject'));
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'activity_name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'achievement' => ['nullable', 'string', 'max:255'],
            'date_recorded' => ['nullable', 'date'],
        ];
    }
}
