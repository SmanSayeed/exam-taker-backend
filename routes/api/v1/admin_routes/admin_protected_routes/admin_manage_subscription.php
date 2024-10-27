<?php

use App\Http\Controllers\Api\V1\Admin\StudentPayment\StudentPaymentController;
use App\Http\Controllers\Api\V1\Admin\Subscription\SubscriptionController;

use Illuminate\Support\Facades\Route;

Route::get('subscriptions', [SubscriptionController::class, 'index']);

Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show']);

Route::post('subscriptions/{subscription}/activate', [SubscriptionController::class, 'activateSubscription']);

Route::post('subscriptions/{subscription}/deactivate', [SubscriptionController::class, 'deactivateSubscription']);

Route::get('student-payments', [StudentPaymentController::class, 'index']);

Route::get('student-payments/{student_payment}', [StudentPaymentController::class, 'show']);

Route::delete('student-payments/{student_payment}', [StudentPaymentController::class, 'destroy']);
