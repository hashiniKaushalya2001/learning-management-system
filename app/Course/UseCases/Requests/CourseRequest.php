<?php

namespace App\Course\UseCases\Requests;

use Dflydev\DotAccessData\Data;
use Illuminate\Validation\Rule;

class CourseRequest extends Data
{
    public ?string $id;

    public ?string $course;

    public static function rules(?int $id = null): array
    {
        if ($id) {
            return [
                'course' => ['required', 'string'],
                'course_id' => [
                    'sometimes',
                    'string',
                    Rule::unique('courses', 'course_id')->ignore($id),
                ],
            ];
        }
        return [
            'department' => ['required', 'string'],
            'courses' => ['required', 'array', 'min:1'],
            'courses.*.course_id' => [
                'required',
                'string',
                Rule::unique('courses', 'course_id'),
            ],
            'courses.*.course' => ['required', 'string'],
        ];
    }
}
