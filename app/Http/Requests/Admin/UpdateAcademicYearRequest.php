<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $yearId = $this->route('academic_year')->id;

        return [
            'year_label' => ['required', 'string', 'max:50', "unique:academic_years,year_label,{$yearId}"],
            'is_current' => ['sometimes', 'boolean'],
        ];
    }
}
