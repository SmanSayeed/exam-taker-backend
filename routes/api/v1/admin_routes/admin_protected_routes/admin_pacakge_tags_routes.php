<?php

use App\Http\Controllers\PackageTagController;
use Illuminate\Support\Facades\Route;

Route::apiResource('package-tags', PackageTagController::class);
