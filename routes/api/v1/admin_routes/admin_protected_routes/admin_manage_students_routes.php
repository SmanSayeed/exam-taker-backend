<?php

use App\Http\Controllers\Api\V1\Admin\ManageStudents\AdminManagesStudent;
use Illuminate\Support\Facades\Route;

Route::get('students', [AdminManagesStudent::class, 'index']);

Route::post('students', [AdminManagesStudent::class, 'store']);

Route::get('students/{student}', [AdminManagesStudent::class, 'show']);

Route::put('students/{student}', [AdminManagesStudent::class, 'update']);

Route::delete('students/{student}', [AdminManagesStudent::class, 'destroy']);

Route::patch('students/{student}/status', [AdminManagesStudent::class, 'changeStatus']);
