<?php

use App\Http\Controllers\Api\V1\Admin\ManageStudents\AdminManagesStudent;
use Illuminate\Support\Facades\Route;

Route::get('manage/students', [AdminManagesStudent::class, 'index']);

Route::post('manage/students', [AdminManagesStudent::class, 'store']);

Route::get('manage/students/{student}', [AdminManagesStudent::class, 'show']);

Route::put('manage/students/{student}', [AdminManagesStudent::class, 'update']);

Route::delete('manage/students/{student}', [AdminManagesStudent::class, 'destroy']);

Route::patch('manage/students/{student}/status', [AdminManagesStudent::class, 'changeStatus']);
