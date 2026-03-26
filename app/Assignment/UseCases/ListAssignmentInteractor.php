<?php

namespace App\Assignment\UseCases;

use App\Assignment\Entities\Models\Assignment;
use Illuminate\Database\Eloquent\Collection;

class ListAssignmentInteractor
{
    public function execute(?string $search = null): Collection
    {
        return Assignment::with(['department', 'course.department'])->get();
    }
}
