<?php

namespace App\Meterial\IO\Http\Controllers;

use App\Department\Entities\Models\Department;
use App\Http\Controllers\Controller;
use App\Meterial\UseCases\DeleteMeterialInteractor;
use App\Meterial\UseCases\ListMeterialInteractor;
use App\Meterial\UseCases\Requests\MeterialRequest;
use App\Meterial\UseCases\StoreMeterialInteractor;
use App\Meterial\UseCases\UpdateMeterialInteractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeterialController extends Controller
{
    public function index(Request $request, ListMeterialInteractor $interactor): JsonResponse
    {
        $materials = $interactor->execute($request->query('search'));

        return response()->json([
            'data' => $materials,
        ]);
    }

    public function store(MeterialRequest $request, StoreMeterialInteractor $interactor): JsonResponse
    {
        $materials = $interactor->execute($request);

        return response()->json([
            'data' => $materials,
            'message' => 'Material created successfully',
        ], 201);
    }

    public function update(int $id, Request $request, UpdateMeterialInteractor $interactor): JsonResponse
    {
        $validated = $request->validate([
            'department' => 'required|exists:departments,id',
            'course_id' => 'required|exists:courses,id',
            'meterial' => 'nullable|file|mimes:pdf|max:2048',
            'aim' => 'nullable|string',
            'lecturer' => 'nullable|string',
            'semester' => 'nullable|string',
            'duration' => 'nullable|string',
        ]);

        $meterial = $interactor->execute($id, $validated);

        return response()->json([
            'data' => $meterial,
            'message' => 'Material updated successfully',
        ]);
    }

    public function destroy(int $id, DeleteMeterialInteractor $interactor): JsonResponse
    {
        $interactor->execute($id);

        return response()->json([
            'message' => 'Material deleted successfully',
        ], 200);
    }

    public function getDepartments(): JsonResponse
    {
        return response()->json([
            'data' => Department::select('id', 'department as name')->get(),
        ]);
    }
}
