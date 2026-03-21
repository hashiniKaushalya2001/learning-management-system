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
        'course' => 'Laravel', // Matches Interactor
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

// it('can update a meterial', function () {
//
//    Storage::fake('public');
//
//    $department = Department::factory()->create();
//    $course = Course::factory()->create();
//
//    $oldFile = UploadedFile::fake()->create('old.pdf', 100);
//
//    $meterial = Meterial::factory()->create([
//        'department' => $department->id,
//        'course_id' => $course->id,
//        'meterial' => 'meterials/old.pdf',
//    ]);
//
//    $newDepartment = Department::factory()->create();
//    $newCourse = Course::factory()->create();
//
//    $newFile = UploadedFile::fake()->create('updated.pdf', 100);
//
//    $payload = [
//        'department' => $newDepartment->id,
//        'course_id' => $newCourse->id,
//        'meterial' => $newFile,
//    ];
//
//    $response = $this->postJson("/api/meterial/{$meterial->id}?_method=PUT", $payload);
//
//    $response->assertStatus(200)
//        ->assertJson([
//            'data' => [
//                'id' => $meterial->id,
//            ],
//            'message' => 'Meterial updated successfully',
//        ]);
//
//    $this->assertDatabaseHas('meterials', [
//        'id' => $meterial->id,
//        'department' => $newDepartment->id,
//        'course_id' => $newCourse->id,
//    ]);
// });
//
// it('can delete a meterial', function () {
//
//    $meterial = Meterial::factory()->create();
//
//    $response = $this->deleteJson("/api/meterial/{$meterial->id}");
//
//    $response->assertStatus(200)
//        ->assertJson([
//            'message' => 'Meterial deleted successfully',
//        ]);
//
//    $this->assertSoftDeleted('meterials', [
//        'id' => $meterial->id,
//    ]);
// });

// it('can fetch department data for a meterial', function () {
//    $department = Department::factory()->create();
//    $course = Course::factory()->create(['department' => $department->id]);
//
//    $response = $this->getJson("/api/courses/{$course->id}/department");
//
//    $response->assertStatus(200)
//        ->assertJson([
//            'department_id' => $department->id,
//        ]);
// });
