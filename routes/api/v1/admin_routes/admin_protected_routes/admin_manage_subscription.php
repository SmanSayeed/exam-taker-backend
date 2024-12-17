<?php

use App\Http\Controllers\Api\V1\Admin\StudentPayment\StudentPaymentController;
use App\Http\Controllers\Api\V1\Admin\Subscription\SubscriptionController;

use Illuminate\Support\Facades\Route;
Route::get('subscriptions', [SubscriptionController::class, 'index']);  // GET list of subscriptions

Route::post('subscriptions', [SubscriptionController::class, 'store']);  // POST create a new subscription

Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show']);  // GET for retrieving a subscription

Route::put('subscriptions/{subscription}', [SubscriptionController::class, 'update']);  // PUT for updating a subscription

Route::delete('subscriptions/{subscription}', [SubscriptionController::class, 'destroy']);  // DELETE for removing a subscription




Route::get('student-payments', [StudentPaymentController::class, 'index']);

Route::get('student-payments/{student_payment}', [StudentPaymentController::class, 'show']);

Route::delete('student-payments/{student_payment}', [StudentPaymentController::class, 'destroy']);

Route::patch('student-payments/{student_payment}/status', [StudentPaymentController::class, 'changeStatus']);
