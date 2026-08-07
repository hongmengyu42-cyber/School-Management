<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $departmentId = $this->route('department')->id;

        return [
            'department_code' => ['required', 'string', 'max:100', "unique:departments,department_code,{$departmentId}"],
            'department_name' => ['required', 'string', 'max:255'],
        ];
    }
}
