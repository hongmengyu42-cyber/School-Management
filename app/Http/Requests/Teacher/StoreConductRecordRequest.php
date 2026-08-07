<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreConductRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('subject'));
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'type' => ['required', 'in:Positive,Negative'],
            'description' => ['required', 'string'],
            'incident_date' => ['required', 'date'],
        ];
    }
}
