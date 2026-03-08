<?php

namespace App\Department\IO\Http\Controllers;

use App\Department\Entities\Models\Department;
use App\Department\UseCases\ListDepartmentInteractor;
use App\Department\UseCases\Requests\DepartmentRequest;
use App\Department\UseCases\StoreDepartmentInteractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController
{
    public function index(ListDepartmentInteractor $listDepartmentInteractor): JsonResponse
    {
        return $listDepartmentInteractor->execute(
            request('search'),
            request('per_page')
        );
    }

    public function store(Request $request, StoreDepartmentInteractor $interactor): JsonResponse
    {
        $validated = $request->validate(DepartmentRequest::rules());

        $department = $interactor->execute($validated);

        return response()->json([
            'data' => $department,
            'message' => 'Department created successfully',
        ], 201);
    }
}
