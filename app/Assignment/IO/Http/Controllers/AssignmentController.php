<?php

namespace App\Assignment\IO\Http\Controllers;

use App\Assignment\Entities\Models\Assignment;
use App\Assignment\UseCases\DeleteAssignmentInteractor;
use App\Assignment\UseCases\ListAssignmentInteractor;
use App\Assignment\UseCases\Requests\AssignmentRequest;
use App\Assignment\UseCases\StoreAssignmentInteractor;
use App\Assignment\UseCases\UpdateAssignmentInteractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentController
{
    public function index(Request $request, ListAssignmentInteractor $interactor): JsonResponse
    {
        $assignments = $interactor->execute($request->query('search'));

        return response()->json([
            'data' => $assignments,
        ]);
    }

    public function store(AssignmentRequest $request, StoreAssignmentInteractor $interactor): JsonResponse
    {
        $assignment = $interactor->execute($request);

        return response()->json($assignment, 201);
    }

    public function update(AssignmentRequest $request, Assignment $assignment, UpdateAssignmentInteractor $interactor): JsonResponse
    {
        $updated = $interactor->execute($assignment, $request);

        return response()->json($updated, 200);
    }

    public function destroy(Assignment $assignment, DeleteAssignmentInteractor $interactor): JsonResponse
    {
        $interactor->execute($assignment);

        return response()->json(['message' => 'Assignment deleted successfully'], 200);
    }
}
