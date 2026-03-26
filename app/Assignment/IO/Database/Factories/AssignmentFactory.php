<?php

namespace App\Assignment\IO\Database\Factories;

use App\Assignment\Entities\Models\Assignment;
use App\Course\Entities\Models\Course;
use App\Department\Entities\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        $department = Department::inRandomOrder()->first();
        $course = Course::inRandomOrder()->first();

        return [
            'department_id' => $department?->id ?? Department::factory(),
            'course_id' => $course?->id ?? Course::factory(),

            'duration' => $this->faker->randomElement(['1 hour', '2 hours', '1 day', '1 week']),
            'instruction' => $this->faker->paragraph(),
            'due_date' => $this->faker->dateTimeBetween('+1 week', '+2 weeks')->format('Y-m-d'),
            'due_time' => $this->faker->time('H:i:s'),
            'file_path' => 'assignments/'.$this->faker->word().'.pdf',
        ];
    }
}
