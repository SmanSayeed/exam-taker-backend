<?php
use App\Http\Controllers\Api\V1\Admin\Auth\AdminCRUDController;
use App\Http\Controllers\Api\V1\Admin\Auth\AdminLogoutController;
use App\Http\Controllers\Api\V1\Admin\Auth\AdminProfileController;
use App\Http\Controllers\Api\V1\Admin\ManageStudents\AdminManagesStudent;
use App\Http\Controllers\Api\V1\Admin\ManageStudents\StudentCRUDController;
use Illuminate\Support\Facades\Route;

Route::get('/profile', [AdminProfileController::class, 'getAdminProfile']);
Route::post('/logout', [AdminLogoutController::class, 'logout']);

Route::apiResource('students', AdminManagesStudent::class)->except(['create', 'edit']);

/* admins */
Route::get('/admins', [AdminCRUDController::class, 'getAllAdmins'])->name('admin.all');
Route::get('/admins/{id}', [AdminCRUDController::class, 'getAdminByID'])->name('admin.get');
Route::put('/admins/{id}', [AdminCRUDController::class, 'editAdmin'])->name('admin.edit');
Route::delete('/admins/{id}', [AdminCRUDController::class, 'deleteAdmin'])->name('admin.delete');
Route::patch('/admins/{id}/toggle-status', [AdminCRUDController::class, 'toggleAdminStatus'])->name('admin.toggleStatus');
