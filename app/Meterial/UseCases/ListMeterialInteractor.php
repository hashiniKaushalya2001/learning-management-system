<?php

namespace App\Meterial\UseCases;

use App\Meterial\Entities\Models\Meterial;
use Illuminate\Http\JsonResponse;

class ListMeterialInteractor
{
    public function execute(?string $search = null)
    {
        $query = Meterial::query();

        if ($search) {
            $query->where('meterial', 'like', "%{$search}%");
        }

        return $query->with('department')->get();
    }
}
