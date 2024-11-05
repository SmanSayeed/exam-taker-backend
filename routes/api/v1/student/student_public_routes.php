<?php

use App\Http\Controllers\Api\V1\Student\Package\PackageController;
use App\Http\Controllers\Api\V1\Student\StudentAuthController;
use Illuminate\Support\Facades\Route;


Route::prefix('student')->group(function () {
    Route::post('/register', [StudentAuthController::class, 'register']);
    Route::post('/login', [StudentAuthController::class, 'login']);
    Route::post('/forgot-password', [StudentAuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [StudentAuthController::class, 'resetPassword']);
    Route::post('/resend-email-verification', [StudentAuthController::class, 'resendEmailVerification']);
    // Package routes
    Route::get('/packages', [PackageController::class, 'index']);
    Route::get('/packages/{package}', [PackageController::class, 'show']);
});

