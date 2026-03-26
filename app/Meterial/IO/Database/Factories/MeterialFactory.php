<?php

namespace App\Meterial\IO\Database\Factories;

use App\Course\Entities\Models\Course;
use App\Department\Entities\Models\Department;
use App\Meterial\Entities\Models\Meterial;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeterialFactory extends Factory
{
    protected $model = Meterial::class;

    public function definition(): array
    {
        return [
            'department' => Department::factory(),
            'course_id' => Course::factory(),
            'meterial' => 'meterials/'.$this->faker->word().'.pdf',
            'aim' => $this->faker->sentence(),
            'lecturer' => $this->faker->name(),
            'semester' => 'Semester 1',
            'duration' => '4 Months',
        ];
    }
}
