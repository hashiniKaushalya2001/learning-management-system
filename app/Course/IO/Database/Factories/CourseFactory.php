<?php

namespace App\Course\IO\Database\Factories;

use App\Course\Entities\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'course' => $this->faker->word(),
        ];
    }
}
