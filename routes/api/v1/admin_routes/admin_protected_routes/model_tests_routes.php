<?php

use App\Http\Controllers\Api\V1\Admin\ModelTest\ModelTestController;
use App\Http\Controllers\Api\V1\Admin\ModelTestQuestionController;
use Illuminate\Support\Facades\Route;

// Grouping all routes under a common prefix
Route::prefix('model-tests')->group(function () {
    // Model Tests Routes
    Route::get('/', [ModelTestController::class, 'index']);
    Route::post('/', [ModelTestController::class, 'store']);
    Route::get('/{modelTest}', [ModelTestController::class, 'show']);
    Route::put('/{modelTest}', [ModelTestController::class, 'update']);
    Route::delete('/{modelTest}', [ModelTestController::class, 'destroy']);
    Route::patch('/{modelTest}/status', [ModelTestController::class, 'changeStatus']);
    Route::post('{modelTest}/attach-questions', [ModelTestController::class, 'attachQuestions']);
    Route::post('{modelTest}/detach-questions', [ModelTestController::class, 'detachQuestions']);
});
