<?php

namespace App\Meterial\UseCases\Requests;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Data;

class MeterialRequest extends Data
{
    #[SpatieRule(['required', 'numeric', 'exists:departments,id'])]
    public int $department;

    #[SpatieRule(['required', 'numeric', 'exists:courses,id'])]
    public int $course_id;

    #[SpatieRule(['required', 'string'])]
    public string $aim;

    #[SpatieRule(['required', 'string'])]
    public string $lecturer;

    #[SpatieRule(['required', 'string'])]
    public string $semester;

    /** @var UploadedFile[] */
    #[SpatieRule(['required', 'array', 'min:1'])]
    public array $meterial;

    #[SpatieRule(['required', 'array', 'min:1'])]
    public array $duration;

    public static function rules(): array
    {
        return [
            'meterial.*' => ['file', 'mimes:pdf', 'max:2048'],
            'duration.*' => ['required', 'string'],
        ];
    }
}
