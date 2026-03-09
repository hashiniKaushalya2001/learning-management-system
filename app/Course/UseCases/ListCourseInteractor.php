<?php

namespace App\Course\UseCases;

use App\Course\Entities\Models\Course;
use Illuminate\Http\JsonResponse;

class ListCourseInteractor
{
    public function execute(?string $search, ?int $perPage = 5): JsonResponse
    {
        $query = Course::query();

        if ($search) {
            $query->where('course', 'like', "%{$search}%");
        }

        $courses = $query->paginate($perPage ?? 5);

        return response()->json([
            'data' => $courses->items(),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ]
        ]);
    }
}
