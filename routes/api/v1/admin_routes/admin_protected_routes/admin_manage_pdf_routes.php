<?php

use App\Http\Controllers\Api\V1\Admin\PDF\PdfController;

use App\Http\Controllers\Api\V1\Admin\PdfSubscription\PdfSubscriptionController;
use App\Http\Controllers\Api\V1\Admin\PdfSubscription\PdfSubscriptionPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('pdfs', [PdfController::class, 'index']);

// Route to create a new PDF
Route::post('pdfs', [PdfController::class, 'store']);

// Route to update a specific PDF
Route::put('pdfs/{pdf}', [PdfController::class, 'update']);

// Route to delete a specific PDF
Route::delete('pdfs/{pdf}', [PdfController::class, 'destroy']);

// Route to show a specific PDF
Route::get('pdfs/{pdf}', [PdfController::class, 'show']);


Route::prefix('pdf-subscriptions')->group(function () {
    Route::get('/', [PdfSubscriptionController::class, 'index']);

    Route::post('/', [PdfSubscriptionController::class, 'store']);

    Route::get('/{pdf_subscription}', [PdfSubscriptionController::class, 'show']);

    Route::put('/{pdf_subscription}', [PdfSubscriptionController::class, 'update']);

    Route::delete('/{pdf_subscription}', [PdfSubscriptionController::class, 'destroy']);
});

Route::prefix('pdf-subscription-payments')->group(function () {
    Route::get('/', [PdfSubscriptionPaymentController::class, 'index']);

    Route::get('/{pdf_subscription_payment}', [PdfSubscriptionPaymentController::class, 'show']);

    Route::delete('/{pdf_subscription_payment}', [PdfSubscriptionPaymentController::class, 'destroy']);

    Route::patch('/{pdf_subscription_payment}/status', [PdfSubscriptionPaymentController::class, 'changeStatus']);
});
