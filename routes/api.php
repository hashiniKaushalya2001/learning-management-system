<?php

use App\Course\IO\Http\Controllers\CourseController;
use App\Department\IO\Http\Controllers\DepartmentController;
use App\Meterial\IO\Http\Controllers\MeterialController;
use App\Students\IO\Http\Controllers\StudentsController;
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

Route::get('/meterial', [MeterialController::class, 'index']);
Route::post('/meterial', [MeterialController::class, 'store']);
Route::put('/meterial/{id}', [MeterialController::class, 'update']);
Route::delete('/meterial/{id}', [MeterialController::class, 'destroy']);

Route::get('/courses/{courseId}/department', [MeterialController::class, 'getDepartmentByCourse']);

Route::get('/students', [StudentsController::class, 'index']);
Route::post('/students', [StudentsController::class, 'store']);
Route::put('/students/{id}', [StudentsController::class, 'update']);
Route::delete('/students/{id}', [StudentsController::class, 'destroy']);

Route::get('/students/{id}', [StudentsController::class, 'showData']);
