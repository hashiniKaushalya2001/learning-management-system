<?php

namespace App\Course\UseCases;

use App\Course\Entities\Models\Course;
use Illuminate\Http\JsonResponse;

class LoadDropdownCourseInteractor
{
    public function execute(): JsonResponse
    {
        $departments = Course::query()
            ->select('department')
            ->distinct()
            ->pluck('department');

        return response()->json([
            'data' => $departments,
        ]);
    }
}
