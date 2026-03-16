<?php

use App\Course\IO\Http\Controllers\CourseController;
use App\Department\IO\Http\Controllers\DepartmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/department', [DepartmentController::class, 'index']);
Route::post('/department', [DepartmentController::class, 'store']);
Route::put('/department/{id}', [DepartmentController::class, 'update']);
Route::delete('/department/{id}', [DepartmentController::class, 'destroy']);

Route::get('/course', [CourseController::class, 'index']);
Route::post('/course', [CourseController::class, 'store']);
Route::put('/course/{id}', [CourseController::class, 'update']);
Route::delete('/course/{id}', [CourseController::class, 'destroy']);
Route::get('/departments', [CourseController::class, 'loadDropdown']);
Route::get('/course/department/{department}', [CourseController::class, 'getByDepartment']);
