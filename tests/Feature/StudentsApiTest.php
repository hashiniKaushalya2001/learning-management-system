<?php

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

    $department = Department::factory()->create([
        'department' => 'Information Technology',
    ]);

    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'birthday' => '2000-01-01',
        'nic' => '200012345678',
        'phone_number' => '0771234567',
        'department' => $department->id,
    ];

    $response = $this->postJson('/api/students', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Student created successfully',
            'data' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ],
        ]);

    $this->assertDatabaseHas('students', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'nic' => '200012345678',
    ]);
});

it('can update a student', function () {

    $student = Students::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $newDepartment = Department::factory()->create([
        'department' => 'Science',
    ]);

    $payload = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'birthday' => '1999-12-31',
        'nic' => '199987654321',
        'phone_number' => '0770000000',
        'department' => $newDepartment->id,
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
        'email' => 'jane@example.com',
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
