<?php

use App\Http\Controllers\Api\V1\Admin\Package\PackageController;
use App\Http\Controllers\Api\V1\Admin\Package\PackagePlanController;
use Illuminate\Support\Facades\Route;

Route::prefix('packages')->group(function () {
    // Package Routes
    Route::get('/', [PackageController::class, 'index']);
    Route::get('/{package}', [PackageController::class, 'show']);
    Route::post('/', [PackageController::class, 'store']);
    Route::put('/{package}', [PackageController::class, 'update']);
    Route::patch('/{package}/status', [PackageController::class, 'changeStatus']); // For changing status
    Route::delete('/{package}', [PackageController::class, 'destroy']);

    // Package Plan Routes
    Route::get('/{package}/plans', [PackagePlanController::class, 'index']); // Get all plans for a package
    Route::get('/plans/{plan}', [PackagePlanController::class, 'show']); // Get a specific plan
    Route::post('/{package}/plans', [PackagePlanController::class, 'store']); // Create a new plan for a package
    Route::put('/plans/{plan}', [PackagePlanController::class, 'update']); // Update a specific plan
    Route::patch('/plans/{plan}/status', [PackagePlanController::class, 'changeStatus']); // Change status of a plan
    Route::delete('/plans/{plan}', [PackagePlanController::class, 'destroy']); // Delete a specific plan
});
