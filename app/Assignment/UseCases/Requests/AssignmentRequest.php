<?php

namespace App\Assignment\UseCases\Requests;

use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Data;

class AssignmentRequest extends Data
{
    #[SpatieRule(['required', 'numeric', 'exists:departments,id'])]
    public int $department_id;

    #[SpatieRule(['required', 'numeric', 'exists:courses,id'])]
    public int $course_id;

    #[SpatieRule(['required', 'string', 'max:255'])]
    public string $duration;

    #[SpatieRule(['required', 'string'])]
    public string $instruction;

    #[SpatieRule(['required', 'date', 'after:today'])]
    public string $due_date;

    #[SpatieRule(['nullable'])]
    public string $due_time;

    #[SpatieRule(['nullable', 'file', 'mimes:pdf', 'max:2048'])]
    public mixed $instruction_file;
}
