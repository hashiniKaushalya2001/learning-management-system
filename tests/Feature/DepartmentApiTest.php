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
