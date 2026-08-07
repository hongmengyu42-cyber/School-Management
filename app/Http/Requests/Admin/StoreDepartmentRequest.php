<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'department_code' => ['required', 'string', 'max:100', 'unique:departments,department_code'],
            'department_name' => ['required', 'string', 'max:255'],
        ];
    }
}
