<?php

namespace App\Course\UseCases;

use App\Course\Entities\Models\Course;
use Illuminate\Http\JsonResponse;

class ListCourseInteractor
{
    public function execute(?string $search): JsonResponse
    {
        $query = Course::query();

        if ($search) {
            $query->where('course', 'like', "%{$search}%");
        }

        $courses = $query->get();

        return response()->json([
            'data' => $courses,
        ]);
    }
}
