<?php

namespace App\Assignment\UseCases;

use App\Assignment\Entities\Models\Assignment;
use Illuminate\Support\Facades\Storage;

class DeleteAssignmentInteractor
{
    public function execute(Assignment $assignment): bool
    {
        if ($assignment->file_path) {
            Storage::disk('public')->delete($assignment->file_path);
        }

        return $assignment->delete();
    }
}
