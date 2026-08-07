<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'year_label' => ['required', 'string', 'max:50', 'unique:academic_years,year_label'],
            'is_current' => ['sometimes', 'boolean'],
        ];
    }
}
