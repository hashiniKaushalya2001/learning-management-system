<?php

namespace App\Students\UseCases\Requests;

use Illuminate\Validation\Rule;
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

    #[SpatieRule(['required', 'string', 'exists:departments,department'])]
    public string $department;

    public static function rules(): array
    {
        $studentId = request()->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('students', 'email')->ignore($studentId),
            ],
            'birthday' => ['required', 'date'],
            'nic' => [
                'required',
                Rule::unique('students', 'nic')->ignore($studentId),
            ],
            'phone_number' => ['required', 'string'],
            'department' => ['required', 'string', 'exists:departments,department'],
        ];
    }
}
