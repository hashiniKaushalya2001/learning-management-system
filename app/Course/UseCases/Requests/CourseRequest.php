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
            'department' => ['required', 'string'],
            'courses' => ['required', 'array', 'min:1'],
            'courses.*.course_id' => ['required', 'string'],
            'courses.*.course' => ['required', 'string'],
        ];
    }
}
