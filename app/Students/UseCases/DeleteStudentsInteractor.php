<?php

namespace App\Students\UseCases;

use App\Students\Entities\Models\Students;
use Illuminate\Http\JsonResponse;

class DeleteStudentsInteractor
{
    public function execute(int $id): JsonResponse
    {
        $student = Students::findOrFail($id);

        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully',
        ], 200);
    }
}
