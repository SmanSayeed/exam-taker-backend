<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\V1\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Api\V1\Admin\Auth\AdminRegistrationController;
use App\Http\Controllers\Api\V1\Admin\Maintenance\AdminMaintenanceController;
use App\Http\Controllers\Api\V1\Questions\QuestionCategoryController;
use App\Http\Controllers\Api\V1\Questions\StudentQuestionCategoryController;
use App\Http\Controllers\Api\V1\Student\ModelTest\ModelTestController;
use App\Http\Controllers\Api\V1\Student\Package\PackageController;
use App\Http\Controllers\Examination\AnswerController;
use App\Http\Controllers\Examination\ExaminationController;
use App\Http\Controllers\Examination\MTAnswerController;
use App\Http\Controllers\Examination\MTAnswerFileController;
use App\Http\Controllers\Examination\MTExaminationController;
use App\Http\Controllers\Examination\MTSubmissionController;
use App\Http\Controllers\ExamQuotaSubscriptionController;

use App\Http\Controllers\StudentController;
use App\Models\Package;
use Illuminate\Support\Facades\Route;
use OpenApi\Generator;
use App\Http\Controllers\StorageController;

Route::get('link-storage', [StorageController::class, 'linkStorage']);


Route::get('/student/config/maximum-free-exam', function() {
    return response()->json([

            'maximum_free_exam' => config('app.maximum_free_exam', 2)
       ]);
});


Route::get('/swagger.json', function () {
    Generator::scan([app_path(),])->toJson();
});

Route::prefix('admin')->group(function () {
    Route::post('/maintenance/clear-cache', [AdminMaintenanceController::class, 'clearCache']);
    Route::post('/maintenance/optimize', [AdminMaintenanceController::class, 'optimize']);


});

Route::prefix('admin')->group(function () {
    Route::post('/exam/create/{model_test_id}', [MTExaminationController::class, 'createExam']);
    Route::get('/model-test-exams/{model_test_id}', [MTExaminationController::class, 'getModelTestExams']);

    Route::get('mt-submissions/{mt_id}/{exam_id}', [MTSubmissionController::class, 'getSubmissionsByExam']);
    Route::get('mt-submissions/{mt_id}/{exam_id}/{student_id}', [MTSubmissionController::class, 'getStudentSubmission']);
    Route::put('mt-submissions/{mt_id}/{exam_id}/{student_id}', [MTSubmissionController::class, 'updateAnswerReview']);

    Route::put('mt-submissions-review/{mt_id}/{exam_id}', [MTSubmissionController::class, 'updateExaminationReviewStatus']);


    Route::get('/model-test-exam-result-with-merit/{student_id}/{model_test_id}', [MTAnswerController::class, 'getStudentResult']);

    Route::get('/model-test-all-students-exam-result/{model_test_id}', [MTAnswerController::class, 'getAllStudentsResult']);
});

Route::prefix('student')->group(function () {

    Route::post('answer-files/upload', [MTAnswerFileController::class, 'upload']);
    Route::get('answer-files/{examId}', [MTAnswerFileController::class, 'getFilesByExam']);

    /* for student package */
     Route::get('/package-categories', [PackageController::class, 'getPackageCategories']);


     Route::get('/all-package-categories', [PackageController::class, 'getAllPackageCategories']);

    /* model test */
    Route::get('/model-test-exams/{model_test_id}', [MTExaminationController::class, 'getModelTestExams']);

    Route::post('/model-test-exam-start',[MTExaminationController::class,'studentStartExam']);

    Route::get('/model-test-result/{student_id}/{model_test_id}', [MTExaminationController::class, 'getMTResult']);

    Route::post('/model-test-exam-finish', [MTAnswerController::class, 'finishExam']);

    Route::get('/model-test-exam-result-with-merit/{student_id}/{model_test_id}', [MTAnswerController::class, 'getStudentResult']);

    Route::get('/model-test-all-students-exam-result/{model_test_id}', [MTAnswerController::class, 'getAllStudentsResult']);

    /* model test XXXXXXXXXXX */

    /*  exam quota subscription */

    Route::post('/exam-quota-subscriptions', [ExamQuotaSubscriptionController::class, 'submitSubscriptionRequest']);


    /* free exam quota subscription XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX */

    Route::post('/exam/start', [ExaminationController::class, 'startExam']);
    Route::post('/exam/{exam_id}/finish', [ExaminationController::class, 'finishExam']);

    Route::get('/exam-details/{examId}', [ExaminationController::class, 'getExamById']);
    Route::get('/exams/student/{studentId}/{withQuestionList}', [ExaminationController::class, 'getExamsByStudent']);
    Route::get('/exams/all/{withQuestionList}', [ExaminationController::class, 'getAllExamsWithStudents']);

    Route::post('/exam-start-with-button-click', [AnswerController::class, 'startExam']);
    Route::post('/exam/finish', [AnswerController::class, 'finishExam']);

    Route::get('/packages', [PackageController::class, 'index']);
    Route::get('/packages/{package}', [PackageController::class, 'show']);

});


/* get category publicly */
Route::get('/student/category/{resourceType}', [StudentQuestionCategoryController::class, 'getData']);
Route::get('/student/category/{resourceType}/{id}', [StudentQuestionCategoryController::class, 'show']);



// Route::prefix('student')->group(function () {
//     Route::post('/register', [StudentController::class, 'store']);
//     Route::post('/login', [AuthController::class, 'login']); // create AuthController for authentication
//     // Other student routes
// });
