<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\V1\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Api\V1\Admin\Auth\AdminRegistrationController;
use App\Http\Controllers\Api\V1\Admin\Maintenance\AdminMaintenanceController;
use App\Http\Controllers\Examination\AnswerController;
use App\Http\Controllers\Examination\ExaminationController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use OpenApi\Generator;

Route::get('/swagger.json', function () {
    Generator::scan([ app_path(), ])->toJson();
});

Route::prefix('admin')->group(function () {
    Route::post('/maintenance/clear-cache', [AdminMaintenanceController::class, 'clearCache']);
    Route::post('/maintenance/optimize', [AdminMaintenanceController::class, 'optimize']);
});

Route::prefix('student')->group(function () {
Route::post('/exam/start', [ExaminationController::class, 'startExam']);
Route::post('/exam/{exam_id}/finish', [ExaminationController::class, 'finishExam']);

Route::get('/exam-details/{examId}', [ExaminationController::class, 'getExamById']);
Route::get('/exams/student/{studentId}/{withQuestionList}', [ExaminationController::class, 'getExamsByStudent']);
Route::get('/exams/all/{withQuestionList}', [ExaminationController::class, 'getAllExamsWithStudents']);

Route::post('/exam-start-with-button-click', [AnswerController::class, 'startExam']);
Route::post('/exam/finish', [AnswerController::class, 'finishExam']);

});



// Route::prefix('student')->group(function () {
//     Route::post('/register', [StudentController::class, 'store']);
//     Route::post('/login', [AuthController::class, 'login']); // create AuthController for authentication
//     // Other student routes
// });
