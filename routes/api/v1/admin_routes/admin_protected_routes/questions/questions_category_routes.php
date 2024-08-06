<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Questions\QuestionBaseController;
use App\Http\Controllers\Api\V1\Questions\QuestionController;


Route::get('/{resourceType}', [QuestionBaseController::class, 'getData']);
Route::get('{resourceType}/{id}', [QuestionBaseController::class, 'show']);
Route::post('{resourceType}', [QuestionBaseController::class, 'store']);
Route::put('{resourceType}/{id}', [QuestionBaseController::class, 'update']);
Route::delete('{resourceType}/{id}', [QuestionBaseController::class, 'destroy']);
Route::patch('{resourceType}/{id}/status', [QuestionBaseController::class, 'changeStatus']);

/* creating questions */

// Create a generic question
Route::post('/create', [QuestionController::class, 'storeQuestion']);

// Create specific types of questions
Route::post('/mcq/{questionId}', [QuestionController::class, 'storeMcqQuestion']);
Route::post('/normal-text/{questionId}', [QuestionController::class, 'storeNormalTextQuestion']);
Route::post('/creative/{questionId}', [QuestionController::class, 'storeCreativeQuestion']);

// Update the status of a specific question
Route::patch('/{id}/status', [QuestionController::class, 'changeQuestionStatus']);

// Retrieve all questions
Route::get('/all', [QuestionController::class, 'getAllQuestions']);

// Retrieve a specific question by ID
Route::get('/single/{id}', [QuestionController::class, 'getQuestion']);
