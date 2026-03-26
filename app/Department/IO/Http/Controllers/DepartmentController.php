<?php

namespace App\Department\IO\Http\Controllers;

use App\Department\UseCases\DeleteDepartmentInteractor;
use App\Department\UseCases\ListDepartmentInteractor;
use App\Department\UseCases\StoreDepartmentInteractor;
use App\Department\UseCases\UpdateDepartmentInteractor;
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
        $validated = $request->validate([
            'department' => 'required|unique:departments,department',
        ]);

        $department = $interactor->execute($validated);

        return response()->json([
            'data' => $department,
            'message' => 'Department created successfully',
        ], 201);
    }

    public function update(int $id, Request $request, UpdateDepartmentInteractor $interactor): JsonResponse
    {

        $validated = $request->validate([
            'department' => 'required|unique:departments,department,'.$id,
        ]);

        $department = $interactor->execute($id, $validated);

        return response()->json([
            'data' => $department,
            'message' => 'Department updated successfully',
        ]);
    }

    public function destroy(int $id, DeleteDepartmentInteractor $interactor): JsonResponse
    {
        $interactor->execute($id);

        return response()->json([
            'message' => 'Department deleted successfully',
        ]);
    }
}
