<?php

namespace App\Meterial\UseCases;

use App\Meterial\Entities\Models\Meterial;

class DeleteMeterialInteractor
{
    public function execute(int $id): void
    {
        $meterial = Meterial::findOrFail($id);
        $meterial->delete();
    }
}
