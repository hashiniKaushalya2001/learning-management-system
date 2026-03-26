<?php

namespace App\Students\IO\Database\Factories;

use App\Department\Entities\Models\Department;
use App\Students\Entities\Models\Students;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentsFactory extends Factory
{
    protected $model = Students::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'birthday' => $this->faker->date(),
            'nic' => $this->faker->numerify('#########V'),
            'phone_number' => $this->faker->phoneNumber(),
            'department' => Department::factory()->create()->department,
        ];
    }
}
