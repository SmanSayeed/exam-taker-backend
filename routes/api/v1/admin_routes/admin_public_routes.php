<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\V1\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Api\V1\Admin\Auth\AdminLogoutController;
use App\Http\Controllers\Api\V1\Admin\Auth\AdminRegistrationController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {


Route::post('image/upload', [GalleryController::class, 'uploadImage']);
Route::put('image/update/{id}', [GalleryController::class, 'updateImage']);
Route::delete('image/delete/{id}', [GalleryController::class, 'deleteImage']);
Route::get('images', [GalleryController::class, 'getImages']);

    Route::get('/check',function(){
        return response()->json(['data'=>"Checked",'message' => 'success'], 200);
    });
    Route::post('/create', [AdminRegistrationController::class, 'store']);
    Route::post('/login', [AdminLoginController::class, 'login']);
});

// Route::prefix('student')->group(function () {
//     Route::post('/register', [StudentController::class, 'store']);
//     Route::post('/login', [AuthController::class, 'login']); // create AuthController for authentication
//     // Other student routes
// });
