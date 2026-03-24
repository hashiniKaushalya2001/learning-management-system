<?php

namespace App\Students\UseCases;

use App\Department\Entities\Models\Department;
use App\Students\Entities\Models\Students;
use App\Students\UseCases\Requests\StudentsRequest;
use Illuminate\Http\JsonResponse;

class StoreStudentsInteractor
{
    public function execute(StudentsRequest $request): JsonResponse
    {
        $department = Department::findOrFail($request->department);

        $student = Students::create([
            'name' => $request->name,
            'email' => $request->email,
            'birthday' => $request->birthday,
            'nic' => $request->nic,
            'phone_number' => $request->phone_number,
            'department' => $department->department,
        ]);

        return response()->json([
            'data' => $student,
            'message' => 'Student created successfully',
        ], 201);
    }
}
