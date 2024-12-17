<?php

use App\Http\Controllers\Api\V1\Student\ModelTest\ModelTestController;
use App\Http\Controllers\Api\V1\Student\PdfSubscription\PdfSubscriptionController;

use App\Http\Controllers\Api\V1\Student\Questions\QuestionController;
use App\Http\Controllers\Api\V1\Student\StudentAuthController;
use App\Http\Controllers\Api\V1\Student\StudentPayment\StudentPaymentController;
use App\Http\Controllers\Api\V1\Student\StudentPdfPayment\PdfSubscriptionPaymentController;
use App\Http\Controllers\Api\V1\Student\Subscription\SubscriptionController;
use Illuminate\Support\Facades\Route;

//package
Route::get('subscriptions', [SubscriptionController::class, 'index']);
Route::get('subscription/{subscription}', [SubscriptionController::class, 'show']);
Route::get('pdf-subscriptions', [PdfSubscriptionController::class, 'index']);
Route::get('pdf-subscription/{pdf_subscription}', [PdfSubscriptionController::class, 'show']);
Route::get('/pdf-subscription-payments', [PdfSubscriptionPaymentController::class, 'index'])->name('student-payments');
Route::get('/pdf-subscription-payments/{pdf_subscription_payment}', [PdfSubscriptionPaymentController::class, 'show']);

Route::post('pay', [StudentPaymentController::class, 'pay']);

Route::post('/verify-email', [StudentAuthController::class, 'verifyEmail']);
Route::get('/subscriptions', [SubscriptionController::class, 'getSubscriptions']);
Route::get('/profile', [StudentAuthController::class, 'getProfile'])->name('profile');
Route::post('/profile', [StudentAuthController::class, 'updateProfile'])->name('profile.update');
Route::get('que/all', [QuestionController::class, 'searchAndFilterQuestions']);

Route::get('/packages/{package}/model-tests', [ModelTestController::class, 'index']);
Route::get('/model-tests/{modelTest}', [ModelTestController::class, 'show']);

Route::get('/student-payments', [StudentPaymentController::class, 'index']);
Route::get('/student-payments/{student_payment}', [StudentPaymentController::class, 'show']);
