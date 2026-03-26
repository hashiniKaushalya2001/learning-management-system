<?php

namespace App\Students\IO\Http\Controllers;

use App\Students\UseCases\DeleteStudentsInteractor;
use App\Students\UseCases\ListStudentsInteractor;
use App\Students\UseCases\Requests\StudentsRequest;
use App\Students\UseCases\ShowStudentsInteractor;
use App\Students\UseCases\StoreStudentsInteractor;
use App\Students\UseCases\UpdateStudentsInteractor;
use Illuminate\Http\JsonResponse;

class StudentsController
{
    public function index(ListStudentsInteractor $interactor): JsonResponse
    {
        $students = $interactor->execute(
            request('search'),
            request('per_page')
        );

        return response()->json([
            'data' => $students,
        ]);
    }

    public function store(StudentsRequest $request, StoreStudentsInteractor $interactor): JsonResponse
    {
        return $interactor->execute($request);
    }

    public function update(int $id, StudentsRequest $request, UpdateStudentsInteractor $interactor): JsonResponse
    {
        return $interactor->execute($id, $request);
    }

    public function destroy(int $id, DeleteStudentsInteractor $interactor): JsonResponse
    {
        return $interactor->execute($id);
    }

    public function showData(string $id, ShowStudentsInteractor $interactor): JsonResponse
    {
        $student = $interactor->execute($id);

        return response()->json([
            'data' => $student,
        ], 200);
    }
}
