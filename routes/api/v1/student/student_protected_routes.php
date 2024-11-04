<?php

use App\Http\Controllers\Api\V1\Student\StudentAuthController;
use App\Http\Controllers\Api\V1\Student\Subscription\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('subscriptions', [SubscriptionController::class, 'index']);
Route::get('subscription/{subscription}', [SubscriptionController::class, 'show']);
Route::post('packages/{package}/subscribe', [SubscriptionController::class, 'subscribe'])
    ->name('packages.subscribe');
Route::post('/verify-email', [StudentAuthController::class, 'verifyEmail']);
Route::get('/subscriptions', [SubscriptionController::class, 'getSubscriptions']);
Route::put('/profile', [StudentAuthController::class, 'updateProfile'])->name('profile.update');
