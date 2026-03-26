<?php

use App\Assignment\Entities\Models\Assignment;
use App\Course\Entities\Models\Course;
use App\Department\Entities\Models\Department;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('can list assgnments', function () {

    Department::factory()->create();
    Course::factory()->create();

    Assignment::factory()->count(3)->create();

    $response = $this->getJson('/api/assignment');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'duration',
                    'instruction',
                    'due_date',
                    'due_time',
                    'course_id',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $this->assertCount(3, $response->json('data'));
});

it('can create an assignment', function () {
    Storage::fake('public');

    $department = Department::factory()->create();
    $course = Course::factory()->create([
        'department' => $department->id,
    ]);

    $file = UploadedFile::fake()->create('assignment_instructions.pdf', 100);

    $payload = [
        'department_id' => $department->id,
        'course_id' => $course->id,
        'duration' => '1 hour',
        'instruction' => 'Test Instr',
        'due_date' => now()->addWeeks(2)->toDateString(),
        'due_time' => '09:00:00',
        'instruction_file' => $file,
    ];

    $response = $this->postJson('/api/assignment', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('assignments', [
        'duration' => '1 hour',
        'course_id' => $course->id,
    ]);

    Storage::disk('public')->assertExists('assignments/'.$file->hashName());
});

it('can update an assignment', function () {
    Storage::fake('public');

    $assignment = Assignment::factory()->create(['duration' => 'Old Duration']);
    $newFile = UploadedFile::fake()->create('updated_brief.pdf', 200);

    $payload = [
        '_method' => 'PUT',
        'department_id' => $assignment->department_id,
        'course_id' => $assignment->course_id,
        'duration' => 'Updated Duration',
        'instruction' => 'Updated instruction.',
        'due_date' => now()->addWeeks(3)->toDateString(),
        'due_time' => '10:00:00',
        'instruction_file' => $newFile,
    ];

    $response = $this->postJson("/api/assignment/{$assignment->id}", $payload);

    $response->assertStatus(200);
    $this->assertDatabaseHas('assignments', [
        'id' => $assignment->id,
        'duration' => 'Updated Duration',
    ]);
});

it('can delete an assignment', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('to_be_deleted.pdf', 100);
    $path = $file->store('assignments', 'public');

    $assignment = Assignment::factory()->create([
        'file_path' => $path,
    ]);

    $this->assertDatabaseHas('assignments', ['id' => $assignment->id]);
    Storage::disk('public')->assertExists($path);

    $response = $this->deleteJson("/api/assignment/{$assignment->id}");

    $response->assertStatus(200);

    $this->assertSoftDeleted('assignments', [
        'id' => $assignment->id,
    ]);

    Storage::disk('public')->assertMissing($path);
});
