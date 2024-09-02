<?php
// app/Http/Controllers/Api/V1/Student/Auth/StudentProfileController.php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource\StudentResource;
use App\Models\Student;
use App\Services\Student\StudentAuthService;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseHelper;
use Exception;

class StudentProfileController extends Controller
{

    protected StudentAuthService $studentAuthService;

    public function __construct(StudentAuthService $studentAuthService)
    {
        $this->adminAuthService = $studentAuthService;
    }

    /**
   * @OA\Get(path="/users", description="Get all users",       operationId="",
   *   @OA\Response(response=200, description="OK",
   *     @OA\JsonContent(type="string")
   *   ),
   *   @OA\Response(response=401, description="Unauthorized"),
   *   @OA\Response(response=404, description="Not Found")
   * )
   */

    public function getStudentProfile(): JsonResponse
    {
        try {

            $student = auth()->guard('student-api')->user();
            return ApiResponseHelper::success(new StudentResource($student), 'Student profile retrieved successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to get admin profile: ' . $e->getMessage(), 500);
        }
    }
}
