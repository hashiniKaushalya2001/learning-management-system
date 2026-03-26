<?php

namespace App\Department\UseCases;

use App\Department\Entities\Models\Department;

class UpdateDepartmentInteractor
{
    public function execute(int $id, array $data): Department
    {
        $department = Department::findOrFail($id);

        $department->update($data);

        return $department;
    }
}
