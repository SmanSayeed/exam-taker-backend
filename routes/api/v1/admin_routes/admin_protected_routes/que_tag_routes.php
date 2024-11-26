<?php

use App\Http\Controllers\Api\V1\Admin\Tag\TagController;
use Illuminate\Support\Facades\Route;

Route::get('tags', [TagController::class, 'index'])->name('tags.index');   // List all tags
Route::post('tags', [TagController::class, 'store'])->name('tags.store');  // Store a new tag
Route::get('tags/{tag}', [TagController::class, 'show'])->name('tags.show');  // Show a specific tag
Route::put('tags/{tag}', [TagController::class, 'update'])->name('tags.update');  // Update a specific tag
Route::delete('tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');  // Delete a specific tag
