<?php
use App\Http\Controllers\Api\V1\Admin\Auth\AdminLogoutController;
use App\Http\Controllers\Api\V1\Admin\Auth\AdminProfileController;
use App\Http\Controllers\Api\V1\Admin\ManageStudents\StudentCRUDController;

Route::get('/profile', [AdminProfileController::class, 'getAdminProfile']);
Route::post('/logout', [AdminLogoutController::class, 'logout']);
Route::apiResource('students', StudentCRUDController::class)->except(['create', 'edit']);



