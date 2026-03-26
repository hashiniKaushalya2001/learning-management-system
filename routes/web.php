<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

Route::get('/departments', function () {
    return Inertia::render('Department/index');
});

Route::get('/course', function () {
    return Inertia::render('Course/index');
});

Route::get('/meterial', function () {
    return Inertia::render('Meterial/index');
});

Route::get('/students', function () {
    return Inertia::render('Students/index');
});

Route::get('/download-pdf/{path}', function ($path) {
    return Storage::disk('local')->response($path);
})->where('path', '.*');
