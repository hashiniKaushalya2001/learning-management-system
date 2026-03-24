<?php

namespace App\Students\UseCases\Requests;

use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Data;

class StudentsRequest extends Data
{
    #[SpatieRule(['required', 'string', 'max:255'])]
    public string $name;

    #[SpatieRule(['required', 'email', 'unique:students,email'])]
    public string $email;

    #[SpatieRule(['required', 'date'])]
    public string $birthday;

    #[SpatieRule(['required', 'unique:students,nic'])]
    public string $nic;

    #[SpatieRule(['required', 'string'])]
    public string $phone_number;

    #[SpatieRule(['required', 'numeric', 'exists:departments,id'])]
    public int $department;

    public static function rules(): array
    {
        return [];
    }
}
