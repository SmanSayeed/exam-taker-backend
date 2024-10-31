<?php

use App\Http\Controllers\Api\V1\Admin\Package\PackageController;
use App\Http\Controllers\Api\V1\Admin\Package\PackagePlanController;
use Illuminate\Support\Facades\Route;

Route::prefix('packages')->group(function () {
    Route::get('/{package}/subscribers', [PackageController::class, 'getPackageSubscribers']);
    Route::get('/', [PackageController::class, 'index']);
    Route::get('/{package}', [PackageController::class, 'show']);
    Route::post('/', [PackageController::class, 'store']);
    Route::put('/{package}', [PackageController::class, 'update']);
    Route::patch('/{package}/status', [PackageController::class, 'changeStatus']); // For changing status
    Route::delete('/{package}', [PackageController::class, 'destroy']);
});
