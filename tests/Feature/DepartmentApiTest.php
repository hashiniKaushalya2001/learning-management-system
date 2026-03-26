<?php

use App\Department\Entities\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can list departments', function () {

    Department::factory()->count(3)->create();

    $response = $this->getJson('/api/department');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'department',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $this->assertCount(3, $response->json('data'));
});

it('can create a department', function () {
    $payload = [
        'department' => 'Finance',
    ];

    $response = $this->postJson('/api/department', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'department' => 'Finance',
            ],
            'message' => 'Department created successfully',
        ]);

    $this->assertDatabaseHas('departments', [
        'department' => 'Finance',
    ]);
});

it('can update a department', function () {

    $department = Department::factory()->create([
        'department' => 'Finance',
    ]);

    $payload = [
        'department' => 'Human Resource',
    ];

    $response = $this->putJson("/api/department/{$department->id}", $payload);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $department->id,
                'department' => 'Human Resource',
            ],
            'message' => 'Department updated successfully',
        ]);

    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'department' => 'Human Resource',
    ]);
});

it('can delete a department', function () {

    $department = Department::factory()->create();

    $response = $this->deleteJson("/api/department/{$department->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Department deleted successfully',
        ]);

    $this->assertSoftDeleted('departments', [
        'id' => $department->id,
    ]);

});

it('validates that a department name must be unique', function () {

    $existing = Department::factory()->create(['department' => 'Engineering']);

    $payload = ['department' => 'Engineering'];
    $response = $this->postJson('/api/department', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['department']);

    $response = $this->putJson("/api/department/{$existing->id}", [
        'department' => 'Engineering',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.department', 'Engineering');
});

it('the department field can not be empty', function () {
    $response = $this->postJson('/api/department', [
        'department' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['department']);

    $response = $this->postJson('/api/department', [
        'department' => '   ',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['department']);

    $department = Department::factory()->create();

    $response = $this->putJson("/api/department/{$department->id}", [
        'department' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['department']);
});
