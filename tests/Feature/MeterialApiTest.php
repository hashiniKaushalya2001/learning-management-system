<?php

use App\Course\Entities\Models\Course;
use App\Department\Entities\Models\Department;
use App\Meterial\Entities\Models\Meterial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('can list meterials', function () {

    Meterial::factory()->count(3)->create();

    $response = $this->getJson('/api/meterial');

    $response = $this->getJson('/api/meterial');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'department',
                    'course_id',
                    'meterial',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $this->assertCount(3, $response->json('data'));
});

it('can create a meterials', function () {
    Storage::fake('local');

    $department = Department::factory()->create([
        'department' => 'IT',
    ]);

    $course = Course::factory()->create([
        'department' => $department->id,
        'course' => 'Laravel',
    ]);

    $file = UploadedFile::fake()->create('notes.pdf', 100);

    $payload = [
        'department' => $department->id,
        'course_id' => $course->id,
        'meterial' => [$file],
        'duration' => ['4 Months'],
        'semester' => 'Semester 1',
        'aim' => 'Test Aim',
        'lecturer' => 'Test Lecturer',
    ];

    $response = $this->postJson('/api/meterial', $payload);

    $response->assertStatus(201);

    $expectedPath = 'it/laravel/semester-1/4-months/'.$file->hashName();

    Storage::disk('local')->assertExists($expectedPath);
});

it('can update a meterial and its files', function () {
    Storage::fake('public');

    $department = Department::factory()->create(['department' => 'IT']);
    $course = Course::factory()->create([
        'department' => $department->id,
        'course' => 'Laravel',
    ]);

    $material = Meterial::factory()->create([
        'department' => $department->id,
        'course_id' => $course->id,
    ]);

    $newFile = UploadedFile::fake()->create('updated_notes.pdf', 200);

    $updatePayload = [
        'department' => $department->id,
        'course_id' => $course->id,
        'meterial' => $newFile,
        'duration' => '6 Months',
        'semester' => 'Semester 2',
        'aim' => 'Updated Aim',
        'lecturer' => 'New Lecturer',
    ];

    $response = $this->putJson("/api/meterial/{$material->id}", $updatePayload);

    $response->assertStatus(200);

    $this->assertDatabaseHas('meterials', [
        'id' => $material->id,
        'semester' => 'Semester 2',
        'lecturer' => 'New Lecturer',
        'duration' => '6 Months',
    ]);

    Storage::disk('public')->assertExists('meterials/'.$newFile->hashName());
});

it('can delete a data and file in meterial', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('lecture_notes.pdf', 500);
    $path = $file->store('meterials', 'public');

    $material = Meterial::factory()->create([
        'meterial' => $path,
    ]);

    $this->assertDatabaseHas('meterials', ['id' => $material->id]);
    Storage::disk('public')->assertExists($path);

    $response = $this->deleteJson("/api/meterial/{$material->id}");

    $response->assertStatus(200);

    $this->assertSoftDeleted('meterials', [
        'id' => $material->id,
    ]);

    Storage::disk('public')->assertMissing($path);
});
