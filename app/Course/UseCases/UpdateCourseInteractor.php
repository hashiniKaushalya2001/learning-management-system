<?php

namespace App\Course\UseCases;

use App\Course\Entities\Models\Course;
use App\Course\UseCases\Requests\CourseRequest;
use Illuminate\Http\JsonResponse;

class UpdateCourseInteractor
{
    public function execute(CourseRequest $request, int $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        $course->update([
            'course' => request()->input('course'),
        ]);

        return response()->json([
            'data' => $course,
        ], 200);
    }
}
