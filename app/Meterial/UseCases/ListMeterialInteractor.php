<?php

namespace App\Meterial\UseCases;

use App\Meterial\Entities\Models\Meterial;
use Illuminate\Database\Eloquent\Collection;

class ListMeterialInteractor
{
    public function execute(?string $search = null): Collection
    {
        return Meterial::query()

            ->with(['department', 'course'])

            ->when($search, function ($query) use ($search) {
                $query->where('lecturer', 'like', "%{$search}%")
                    ->orWhere('semester', 'like', "%{$search}%")
                    ->orWhere('aim', 'like', "%{$search}%")

                    ->orWhereHas('course', function ($q) use ($search) {
                        $q->where('course', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->get();
    }
}
