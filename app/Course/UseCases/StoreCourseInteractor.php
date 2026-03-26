<?php

namespace App\Course\UseCases;

use App\Course\Entities\Models\Course;
use App\Course\UseCases\Requests\CourseRequest;
use Illuminate\Http\JsonResponse;

class StoreCourseInteractor
{
    public function execute(CourseRequest $request): JsonResponse
    {
        $departmentName = request()->input('department');

        $courses = request()->input('courses', []);

        $createdCourses = [];

        foreach ($courses as $c) {
            $createdCourses[] = Course::create([
                'course_id' => $c['course_id'] ?? null,
                'course' => $c['course'] ?? null,
                'department' => $departmentName,
            ]);
        }

        return response()->json([
            'data' => $createdCourses,
        ], 201);
    }
}
