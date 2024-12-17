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
    Route::get('/', [PdfSubscriptionController::class, 'index'])->name('api.pdf.subscriptions.index');

    Route::get('/{pdf_subscription}', [PdfSubscriptionController::class, 'show'])->name('api.pdf.subscriptions.show');

    Route::patch('/{pdf_subscription}/activate', [PdfSubscriptionController::class, 'activateSubscription'])->name('api.pdf.subscriptions.activate');

    Route::patch('/{pdf_subscription}/deactivate', [PdfSubscriptionController::class, 'deactivateSubscription'])->name('api.pdf.subscriptions.deactivate');
});

Route::prefix('pdf-subscription-payments')->group(function () {
    Route::get('/', [PdfSubscriptionPaymentController::class, 'index'])->name('api.pdf.subscription_payments.index');

    Route::get('/{pdf_subscription_payment}', [PdfSubscriptionPaymentController::class, 'show'])->name('api.pdf.subscription_payments.show');

    Route::delete('/{pdf_subscription_payment}', [PdfSubscriptionPaymentController::class, 'destroy'])->name('api.pdf.subscription_payments.destroy');
});
