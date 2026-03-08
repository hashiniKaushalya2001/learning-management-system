<?php

namespace App\Department\IO\Database\Factories;

use App\Department\Entities\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition()
    {
        return [
            'department' => $this->faker->company(),
        ];
    }
}
