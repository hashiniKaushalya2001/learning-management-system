<?php

namespace App\Course\UseCases;

use App\Department\Entities\Models\Department;
use Illuminate\Http\JsonResponse;

class LoadDropdownCourseInteractor
{
    public function execute(): JsonResponse
    {
        $departments = Department::query()
            ->select('id', 'department')
            ->get();

        return response()->json([
            'data' => $departments,
        ]);
    }
}
