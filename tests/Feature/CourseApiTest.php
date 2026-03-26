<?php

use App\Course\Entities\Models\Course;

test('can list courses', function () {

    Course::factory()->count(3)->create();

    $response = $this->getJson('/api/course');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'course',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $this->assertCount(3, $response->json('data'));
});

test('can create courses', function () {

    $data = [
        'department' => 'Finance',
        'courses' => [
            [
                'course_id' => 'C001',
                'course' => 'Accounting',
            ],
            [
                'course_id' => 'C002',
                'course' => 'Banking',
            ],
        ],
    ];

    $response = $this->postJson('/api/course', $data);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'course_id',
                    'course',
                    'department',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $this->assertDatabaseHas('courses', [
        'course_id' => 'C001',
        'course' => 'Accounting',
        'department' => 'Finance',
    ]);

    $this->assertDatabaseHas('courses', [
        'course_id' => 'C002',
        'course' => 'Banking',
        'department' => 'Finance',
    ]);
});

test('can update a courses', function () {

    $course = Course::factory()->create([
        'course' => 'Old Course Name',
    ]);

    $data = [
        'id' => $course->id,
        'course' => 'Updated Course Name',
    ];

    $response = $this->putJson("/api/course/{$course->id}", $data);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'course',
                'created_at',
                'updated_at',
            ],
        ]);

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'course' => 'Updated Course Name',
    ]);
});

test('can delete a courses', function () {

    $course = Course::factory()->create([
        'course' => 'Course To Delete',
    ]);

    $response = $this->deleteJson("/api/course/{$course->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
        ]);

    $this->assertSoftDeleted('courses', [
        'id' => $course->id,
    ]);
});

test('can load data for dropdown', function () {

    Course::factory()->create([
        'department' => 'IT',
    ]);

    Course::factory()->create([
        'department' => 'Finance',
    ]);

    Course::factory()->create([
        'department' => 'IT',
    ]);

    $response = $this->getJson('/api/departments');

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'IT',
                'Finance',
            ],
        ]);

});

test('can load data for table', function () {

    Course::factory()->create([
        'course_id' => 'C101',
        'course' => 'Networking',
        'department' => 'IT',
    ]);

    Course::factory()->create([
        'course_id' => 'C102',
        'course' => 'Cybersecurity',
        'department' => 'IT',
    ]);

    Course::factory()->create([
        'course_id' => 'C201',
        'course' => 'Accounting',
        'department' => 'Finance',
    ]);

    $response = $this->getJson('/api/course/department/IT');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'course_id',
                    'course',
                    'department',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $data = $response->json('data');
    $this->assertCount(2, $data);
    foreach ($data as $course) {
        $this->assertEquals('IT', $course['department']);
    }
});

it('validates that a course id must be unique', function () {
    $existing = Course::factory()->create([
        'course_id' => 'CS101',
        'course' => 'Computer Science',
        'department' => 'IT',
    ]);

    $payload = [
        'department' => 'IT',
        'courses' => [
            ['course_id' => 'CS101', 'course' => 'Different Course'],
        ],
    ];
    $this->postJson('/api/course', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['courses.0.course_id']);

    $updateData = [
        'id' => $existing->id,
        'course' => 'Updated CS Name',
    ];

    $this->putJson("/api/course/{$existing->id}", $updateData)
        ->assertStatus(200)
        ->assertJsonPath('data.course', 'Updated CS Name');
});

it('requires at least one course when creating a department', function () {
    $payload = [
        'department' => 'IT',
        'courses' => [],
    ];

    $response = $this->postJson('/api/course', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['courses']);
});
