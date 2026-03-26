<?php

namespace App\Meterial\UseCases;

use App\Meterial\Entities\Models\Meterial;
use Illuminate\Http\UploadedFile;

class UpdateMeterialInteractor
{
    public function execute(int $id, array $data): Meterial
    {
        $meterial = Meterial::findOrFail($id);

        // Check if a new file was actually uploaded
        if (isset($data['meterial']) && $data['meterial'] instanceof UploadedFile) {
            // Option: Delete the old file here if you want to save space
            // Storage::disk('public')->delete($meterial->meterial);

            $path = $data['meterial']->store('meterials', 'public');
            $data['meterial'] = $path;
        }

        $meterial->update($data);

        return $meterial->fresh();
    }
}
