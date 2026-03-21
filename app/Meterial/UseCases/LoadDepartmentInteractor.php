<?php

namespace App\Meterial\UseCases;

use App\Course\Entities\Models\Course;
use Illuminate\Http\JsonResponse;

class LoadDepartmentInteractor
{
    public function execute(int $courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);

        return response()->json([
            'department_id' => $course->department,
        ]);
    }
}
