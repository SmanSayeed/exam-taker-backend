<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Questions\QuestionBaseController;
use App\Http\Controllers\Api\V1\Questions\QuestionController;

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

/* update */
Route::put('/{id}', [QuestionController::class, 'updateQuestion']);

// MCQ question update
Route::put('/mcq/{id}', [QuestionController::class, 'updateMcqQuestion']);

// Normal text question update
Route::put('/normal-text/{id}', [QuestionController::class, 'updateNormalTextQuestion']);

// Creative question update
Route::put('/creative/{id}', [QuestionController::class, 'updateCreativeQuestion']);
