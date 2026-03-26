<?php

use App\Course\Entities\Models\Course;
use App\Department\Entities\Models\Department;
use App\Students\Entities\Models\Students;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can list students', function () {

    Students::factory()->count(3)->create();

    $response = $this->getJson('/api/students');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'birthday',
                    'nic',
                    'phone_number',
                    'department',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $this->assertCount(3, $response->json('data'));
});

it('can create a student', function () {
    $deptName = 'Information Technology';

    Department::factory()->create([
        'department' => $deptName,
    ]);

    Course::create([
        'department' => $deptName,
        'course' => 'BSc in IT',
    ]);

    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'birthday' => '2000-01-01',
        'nic' => '200012345678',
        'phone_number' => '0771234567',
        'department' => $deptName,
    ];

    $response = $this->postJson('/api/students', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Student created successfully',
            'data' => [
                'name' => 'John Doe',
                'department' => $deptName,
            ],
        ]);

    $this->assertDatabaseHas('students', [
        'name' => 'John Doe',
        'department' => $deptName,
    ]);
});

it('can update a student', function () {
    $student = Students::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    Department::factory()->create([
        'department' => 'Science',
    ]);

    $payload = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'birthday' => '1999-12-31',
        'nic' => '199987654321',
        'phone_number' => '0770000000',
        'department' => 'Science',
    ];

    $response = $this->putJson("/api/students/{$student->id}", $payload);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Student updated successfully',
            'data' => [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
            ],
        ]);

    $this->assertDatabaseHas('students', [
        'id' => $student->id,
        'name' => 'Jane Doe',
        'department' => 'Science',
    ]);
});

it('can delete a student', function () {

    $student = Students::factory()->create();

    $response = $this->deleteJson("/api/students/{$student->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Student deleted successfully',
        ]);

    $this->assertSoftDeleted('students', [
        'id' => $student->id,
    ]);
});

it('can show a student details', function () {

    $student = Students::factory()->create([
        'name' => 'Alice Smith',
        'email' => 'alice@example.com',
    ]);

    $response = $this->getJson("/api/students/{$student->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'birthday',
                'nic',
                'phone_number',
                'department',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJson([
            'data' => [
                'id' => $student->id,
                'name' => 'Alice Smith',
                'email' => 'alice@example.com',
            ],
        ]);
});
