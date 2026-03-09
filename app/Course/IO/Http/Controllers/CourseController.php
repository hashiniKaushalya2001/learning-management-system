<?php

namespace App\Course\IO\Http\Controllers;

use App\Course\UseCases\ListCourseInteractor;
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
}
