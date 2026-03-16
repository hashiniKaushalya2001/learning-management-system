<?php

namespace App\Course\UseCases\Requests;

use Dflydev\DotAccessData\Data;
use Illuminate\Validation\Rule;

class CourseRequest extends Data
{
    public ?string $id;

    public ?string $course;

    public static function rules(): array
    {
        return [
            'course' => [
                'required',
                Rule::unique('courses', 'course')
                    ->ignore(request()->input('id')),
            ],
        ];
    }
}
