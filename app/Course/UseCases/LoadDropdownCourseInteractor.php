<?php

namespace App\Course\UseCases;

use App\Department\Entities\Models\Department;
use Illuminate\Http\JsonResponse;

class LoadDropdownCourseInteractor
{
    public function execute(): JsonResponse
    {
        $departments = Department::query()
            ->pluck('department');

        return response()->json([
            'data' => $departments,
        ]);
    }
}
