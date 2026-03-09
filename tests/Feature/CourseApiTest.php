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
