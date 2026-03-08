<?php

use App\Department\IO\Http\Controllers\DepartmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/department', [DepartmentController::class, 'index']);
Route::post('/department', [DepartmentController::class, 'store']);
