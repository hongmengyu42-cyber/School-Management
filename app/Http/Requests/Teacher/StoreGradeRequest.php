<?php

namespace App\Http\Requests\Teacher;

use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $subject = Subject::findOrFail($this->route('subject')->id);

        return $this->user()->can('create', [\App\Models\Grade::class, $subject]);
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'category_id' => ['nullable', 'exists:grade_categories,id'],
            'label' => ['nullable', 'string', 'max:255'],
            'grade_value' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
