<?php

namespace App\Course\IO\Http\Controllers;

use App\Course\UseCases\DeleteCourseInteractor;
use App\Course\UseCases\GetCoursesByDepartmentInteractor;
use App\Course\UseCases\ListCourseInteractor;
use App\Course\UseCases\LoadDropdownCourseInteractor;
use App\Course\UseCases\Requests\CourseRequest;
use App\Course\UseCases\StoreCourseInteractor;
use App\Course\UseCases\UpdateCourseInteractor;
use Illuminate\Http\JsonResponse;

class CourseController
{
    public function index(ListCourseInteractor $listCourseInteractor): JsonResponse
    {
        return $listCourseInteractor->execute(
            request('search'),
            request('per_page')
        );
    }

    public function store(CourseRequest $request, StoreCourseInteractor $storeCourseInteractor): JsonResponse
    {
        request()->validate(CourseRequest::rules());

        return $storeCourseInteractor->execute($request);
    }

    public function update(CourseRequest $request, UpdateCourseInteractor $updateCourseInteractor, int $id): JsonResponse
    {
        request()->validate(CourseRequest::rules($id));

        return $updateCourseInteractor->execute($request, $id);
    }

    public function destroy(DeleteCourseInteractor $deleteCourseInteractor, int $id): JsonResponse
    {
        return $deleteCourseInteractor->execute($id);
    }

    public function loadDropdown(LoadDropdownCourseInteractor $interactor): JsonResponse
    {
        return $interactor->execute();
    }

    public function getByDepartment(GetCoursesByDepartmentInteractor $interactor, string $department): JsonResponse
    {
        return $interactor->execute($department);
    }
}
