<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Questions\QuestionBaseController;


Route::get('/{resourceType}', [QuestionBaseController::class, 'getData']);
Route::get('{resourceType}/{id}', [QuestionBaseController::class, 'show']);
Route::post('{resourceType}', [QuestionBaseController::class, 'store']);
Route::put('{resourceType}/{id}', [QuestionBaseController::class, 'update']);
Route::delete('{resourceType}/{id}', [QuestionBaseController::class, 'destroy']);
Route::patch('{resourceType}/{id}/status', [QuestionBaseController::class, 'changeStatus']);

