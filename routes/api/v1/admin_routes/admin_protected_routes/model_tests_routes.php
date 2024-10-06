<?php

use App\Http\Controllers\Api\V1\Admin\ModelTest\ModelTestController;
use Illuminate\Support\Facades\Route;

Route::get('model-tests', [ModelTestController::class, 'index']);

Route::post('model-tests', [ModelTestController::class, 'store']);

Route::get('model-tests/{modelTest}', [ModelTestController::class, 'show']);

Route::put('model-tests/{modelTest}', [ModelTestController::class, 'update']);

Route::delete('model-tests/{modelTest}', [ModelTestController::class, 'destroy']);

Route::patch('model-tests/{modelTest}/status', [ModelTestController::class, 'changeStatus']);
