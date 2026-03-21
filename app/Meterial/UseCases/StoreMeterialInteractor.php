<?php

namespace App\Meterial\UseCases;

use App\Course\Entities\Models\Course;
use App\Department\Entities\Models\Department;
use App\Meterial\Entities\Models\Meterial;
use App\Meterial\UseCases\Requests\MeterialRequest;
use Illuminate\Support\Str;

class StoreMeterialInteractor
{
    public function execute(MeterialRequest $request): array
    {

        $department = Department::find($request->department);
        $course = Course::find($request->course_id);

        $deptName = Str::slug($department?->department ?? 'unknown-dept');
        $courseSlug = Str::slug($course?->course ?? 'unknown-course');
        $semesterSlug = Str::slug($request->semester);

        $createdMaterials = [];

        foreach ($request->meterial as $index => $file) {

            $duration = $request->duration[$index] ?? 'unknown-duration';
            $safeDuration = Str::slug($duration);

            $folderPath = "{$deptName}/{$courseSlug}/{$semesterSlug}/{$safeDuration}";

            $filePath = $file->store($folderPath, 'local');

            $createdMaterials[] = Meterial::create([
                'department' => $request->department,
                'course_id' => $request->course_id,
                'aim' => $request->aim,
                'lecturer' => $request->lecturer,
                'semester' => $request->semester,
                'meterial' => $filePath,
                'duration' => $duration,
            ]);
        }

        return $createdMaterials;
    }
}
