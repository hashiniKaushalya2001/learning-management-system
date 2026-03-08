<?php

namespace App\Department\UseCases\Requests;

use Dflydev\DotAccessData\Data;
use Illuminate\Validation\Rule;

class DepartmentRequest extends Data
{
    public ?string $department;

    public static function rules(): array
    {
        return [
            'department' => [
                'required',
                Rule::unique('departments', 'department'),
            ],
        ];
    }
}
