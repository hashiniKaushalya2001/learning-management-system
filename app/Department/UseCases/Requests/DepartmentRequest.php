<?php

namespace App\Department\UseCases\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
{
    public function rules(): array
    {
        $departmentId = $this->route('department');

        return [
            'department' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'department')->ignore($departmentId),
            ],
        ];
    }
}
