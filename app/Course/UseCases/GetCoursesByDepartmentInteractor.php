<?php

namespace App\Course\UseCases;

use App\Course\Entities\Models\Course;
use Illuminate\Http\JsonResponse;

class GetCoursesByDepartmentInteractor
{
    public function execute(string $department): JsonResponse
    {
        $courses = Course::where('department', $department)->get();

        return response()->json([
            'data' => $courses,
        ], 200);
    }
}
