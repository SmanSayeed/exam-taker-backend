<?php

use App\Http\Controllers\Api\V1\Student\ModelTest\ModelTestController;
use App\Http\Controllers\Api\V1\Student\Questions\QuestionController;
use App\Http\Controllers\Api\V1\Student\StudentAuthController;
use App\Http\Controllers\Api\V1\Student\Subscription\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('subscriptions', [SubscriptionController::class, 'index']);
Route::get('subscription/{subscription}', [SubscriptionController::class, 'show']);
Route::post('packages/{package}/subscribe', [SubscriptionController::class, 'subscribe'])
    ->name('packages.subscribe');
Route::post('/verify-email', [StudentAuthController::class, 'verifyEmail']);
Route::get('/subscriptions', [SubscriptionController::class, 'getSubscriptions']);
Route::get('/profile', [StudentAuthController::class, 'getProfile'])->name('profile');
Route::post('/profile', [StudentAuthController::class, 'updateProfile'])->name('profile.update');
Route::get('que/all', [QuestionController::class, 'searchAndFilterQuestions']);

Route::get('/packages/{package}/model-tests', [ModelTestController::class, 'index']);
Route::get('/model-tests/{modelTest}', [ModelTestController::class, 'show']);
