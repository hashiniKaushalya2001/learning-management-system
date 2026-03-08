<?php

namespace App\Department\UseCases;

use App\Department\Entities\Models\Department;
use Illuminate\Http\JsonResponse;

class ListDepartmentInteractor
{
    public function execute(?string $search = null, ?int $perPage = 5): JsonResponse
    {
        $query = Department::query();

        if ($search) {
            $query->where('department', 'like', "%{$search}%");
        }

        $departments = $query->paginate($perPage ?? 5);

        return response()->json([
            'data' => $departments->items()
        ]);
    }
}
