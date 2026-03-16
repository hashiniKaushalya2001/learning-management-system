<?php

namespace App\Department\UseCases;

use App\Department\Entities\Models\Department;

class DeleteDepartmentInteractor
{
    public function execute(int $id): void
    {
        $department = Department::findOrFail($id);

        $department->delete();
    }
}
