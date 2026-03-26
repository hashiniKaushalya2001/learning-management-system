<?php

namespace App\Assignment\UseCases;

use App\Assignment\Entities\Models\Assignment;
use App\Assignment\UseCases\Requests\AssignmentRequest;
use Illuminate\Http\UploadedFile;

class StoreAssignmentInteractor
{
    public function execute(AssignmentRequest $request): Assignment
    {
        $data = $request->toArray();

        if ($request->instruction_file instanceof UploadedFile) {
            $data['file_path'] = $request->instruction_file->store('assignments', 'public');
        }

        return Assignment::create([
            'duration' => $data['duration'],
            'instruction' => $data['instruction'],
            'due_date' => $data['due_date'],
            'due_time' => $data['due_time'] ?? null,
            'course_id' => $data['course_id'],
            'department_id' => $data['department_id'],
            'file_path' => $data['file_path'] ?? null,
        ]);
    }
}
