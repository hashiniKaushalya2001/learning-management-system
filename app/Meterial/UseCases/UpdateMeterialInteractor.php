<?php

namespace App\Meterial\UseCases;

use App\Meterial\Entities\Models\Meterial;
use Illuminate\Http\UploadedFile;

class UpdateMeterialInteractor
{
    public function execute(int $id, array $data): Meterial
    {
        $meterial = Meterial::findOrFail($id);

        if (isset($data['meterial']) && $data['meterial'] instanceof UploadedFile) {
            $path = $data['meterial']->store('meterials', 'public');
            $data['meterial'] = $path;
        }

        $meterial->update($data);

        return $meterial->fresh();
    }
}
