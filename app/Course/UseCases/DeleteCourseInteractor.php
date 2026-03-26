<?php

namespace App\Course\UseCases;

use App\Course\Entities\Models\Course;
use Illuminate\Http\JsonResponse;

class DeleteCourseInteractor
{
    public function execute(int $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully.',
        ], 200);
    }
}
