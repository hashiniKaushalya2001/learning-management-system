<?php

namespace App\Department\UseCases;

use App\Department\Entities\Models\Department;
use App\Department\UseCases\Requests\DepartmentRequest;

class StoreDepartmentInteractor
{
    public function execute(array $validated): Department
    {
        return Department::create([
            'department' => $validated['department'],
        ]);
    }
}
