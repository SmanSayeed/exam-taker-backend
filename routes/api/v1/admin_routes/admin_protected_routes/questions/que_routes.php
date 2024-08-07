<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Questions\QuestionBaseController;
use App\Http\Controllers\Api\V1\Questions\QuestionController;

use App\Http\Controllers\Api\V1\Questions\QuestionableController;

/* creating questions */

// Create a generic question
Route::post('/create', [QuestionController::class, 'storeQuestion']);

// Create specific types of questions
Route::post('/mcq', [QuestionController::class, 'storeMcqQuestion']);
Route::post('/normal-text', [QuestionController::class, 'storeNormalTextQuestion']);
Route::post('/creative', [QuestionController::class, 'storeCreativeQuestion']);

// Update the status of a specific question
Route::patch('/{id}/status', [QuestionController::class, 'changeQuestionStatus']);

// Retrieve all questions
Route::get('/all', [QuestionController::class, 'getAllQuestions']);

// Retrieve a specific question by ID
Route::get('/single/{id}', [QuestionController::class, 'getQuestion']);

/* update */
Route::put('/update/{id}', [QuestionController::class, 'updateQuestion']);

// MCQ question update
Route::put('/update/mcq/{id}', [QuestionController::class, 'updateMcqQuestion']);

// Normal text question update
Route::put('/update/normal-text/{id}', [QuestionController::class, 'updateNormalTextQuestion']);

// Creative question update
Route::put('/update/creative/{id}', [QuestionController::class, 'updateCreativeQuestion']);


// Generic Question Routes
Route::delete('/delete/{id}', [QuestionController::class, 'deleteQuestion']);

// MCQ Question Routes
Route::delete('/delete/mcq/{id}', [QuestionController::class, 'deleteMcqQuestion']);

// Normal Text Question Routes
Route::delete('/delete/normal-text/{id}', [QuestionController::class, 'deleteNormalTextQuestion']);

// Creative Question Routes
Route::delete('/delete/creative/{id}', [QuestionController::class, 'deleteCreativeQuestion']);

// Define routes for MCQ questions
Route::get('/mcq', [QuestionController::class, 'getAllMcqQuestions']);
Route::get('/mcq/{id}', [QuestionController::class, 'getMcqQuestion']);

// Define routes for Creative questions
Route::get('/creative', [QuestionController::class, 'getAllCreativeQuestions']);
Route::get('/creative/{id}', [QuestionController::class, 'getCreativeQuestion']);



Route::post('questionables/{questionable}/attach', [QuestionableController::class, 'attachTypes']);
Route::post('questionables/{questionable}/detach', [QuestionableController::class, 'detachTypes']);

