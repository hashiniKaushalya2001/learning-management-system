<?php

namespace App\Students\UseCases;

use App\Students\Entities\Models\Students;

class ListStudentsInteractor
{
    public function execute(?string $search = null, ?int $perPage = 5)
    {
        $query = Students::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        $students = $query->paginate($perPage ?? 5);

        return $students->items();
    }
}
