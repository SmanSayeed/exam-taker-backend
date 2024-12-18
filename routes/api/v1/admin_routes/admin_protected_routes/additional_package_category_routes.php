<?php

use App\Http\Controllers\Api\V1\Admin\AdditionalPackageCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('additional-package-categories', [AdditionalPackageCategoryController::class, 'index']);

Route::post('additional-package-categories', [AdditionalPackageCategoryController::class, 'store']);

Route::get('additional-package-categories/{additional_package_category}', [AdditionalPackageCategoryController::class, 'show']);

Route::put('additional-package-categories/{additional_package_category}', [AdditionalPackageCategoryController::class, 'update']);

Route::delete('additional-package-categories/{additional_package_category}', [AdditionalPackageCategoryController::class, 'destroy']);
