<?php

namespace App\Students\UseCases;

use App\Students\Entities\Models\Students;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ShowStudentsInteractor
{
    public function execute(string $id): Students
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:students,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return Students::findOrFail($id);
    }
}
