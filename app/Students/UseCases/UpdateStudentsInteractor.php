<?php

namespace App\Students\UseCases;

use App\Department\Entities\Models\Department;
use App\Students\Entities\Models\Students;
use App\Students\UseCases\Requests\StudentsRequest;
use Illuminate\Http\JsonResponse;

class UpdateStudentsInteractor
{
    public function execute(int $id, StudentsRequest $request): JsonResponse
    {
        $student = Students::findOrFail($id);
        $department = Department::where('department', $request->department)->firstOrFail();

        $student->update([
            'name' => $request->name,
            'email' => $request->email,
            'birthday' => $request->birthday,
            'nic' => $request->nic,
            'phone_number' => $request->phone_number,
            'department' => $department->department,
        ]);

        return response()->json([
            'data' => $student,
            'message' => 'Student updated successfully',
        ], 200);
    }
}
