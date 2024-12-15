<?php

use App\Http\Controllers\Api\V1\Admin\Package\PackageController;
use Illuminate\Support\Facades\Route;

Route::prefix('packages')->group(function () {
    Route::get('/{package}/subscribers', [PackageController::class, 'getPackageSubscribers']);
    Route::get('/', [PackageController::class, 'index']);
    Route::get('/{package}', [PackageController::class, 'show']);
    Route::post('/', [PackageController::class, 'store']);
    Route::put('/{package}', [PackageController::class, 'update']);
    Route::patch('/{package}/status', [PackageController::class, 'changeStatus']); // For changing status
    Route::delete('/{package}', [PackageController::class, 'destroy']);
    Route::post('{package}/attach-pdf', [PackageController::class, 'attachPdf']);
    Route::post('{package}/detach-pdf', [PackageController::class, 'detachPdf']);
    //tag attaching detaching routes
    Route::post('{package}/attach-tag', [PackageController::class, 'attachTag']);
    Route::post('{package}/detach-tag', [PackageController::class, 'detachTag']);
});
