<?php

namespace App\Assignment\UseCases;

use App\Assignment\Entities\Models\Assignment;
use App\Assignment\UseCases\Requests\AssignmentRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateAssignmentInteractor
{
    public function execute(Assignment $assignment, AssignmentRequest $request): Assignment
    {
        $data = $request->toArray();

        if ($request->instruction_file instanceof UploadedFile) {
            if ($assignment->file_path) {
                Storage::disk('public')->delete($assignment->file_path);
            }
            $data['file_path'] = $request->instruction_file->store('assignments', 'public');
        }

        $assignment->update([
            'department_id' => $data['department_id'],
            'course_id' => $data['course_id'],
            'duration' => $data['duration'],
            'instruction' => $data['instruction'],
            'due_date' => $data['due_date'],
            'due_time' => $data['due_time'] ?? $assignment->due_time,
            'file_path' => $data['file_path'] ?? $assignment->file_path,
        ]);

        return $assignment->fresh();
    }
}
