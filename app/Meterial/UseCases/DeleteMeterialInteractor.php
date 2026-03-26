<?php

namespace App\Meterial\UseCases;

use App\Meterial\Entities\Models\Meterial;
use Illuminate\Support\Facades\Storage;

class DeleteMeterialInteractor
{
    public function execute(int $id): void
    {
        $material = Meterial::findOrFail($id);

        if ($material->meterial && Storage::disk('public')->exists($material->meterial)) {
            Storage::disk('public')->delete($material->meterial);
        }

        $material->delete();
    }
}
