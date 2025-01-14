<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\DTOs\StudentDTO\StudentLoginData;
use App\DTOs\StudentDTO\StudentRegistrationData;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StudentForgotPasswordRequest;
use App\Http\Requests\Student\StudentLoginRequest;
use App\Http\Requests\Student\StudentProfileUpdateRequest;
use App\Http\Requests\Student\StudentRegistrationRequest;
use App\Http\Requests\Student\StudentResetPasswordRequest;
use App\Http\Resources\StudentResource\StudentResource;
use App\Models\Student;
use App\Services\Student\StudentAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StudentAuthController extends Controller
{
    protected StudentAuthService $studentAuthService;

    public function __construct(StudentAuthService $studentAuthService)
    {
        $this->studentAuthService = $studentAuthService;
    }

    public function login(StudentLoginRequest $request): JsonResponse
    {
        $loginData = StudentLoginData::from($request->validated());
        return $this->studentAuthService->authenticate($loginData);
    }

    public function register(StudentRegistrationRequest $request): JsonResponse
    {
        $registrationData = StudentRegistrationData::from($request->validated());
        return $this->studentAuthService->register($registrationData);
    }

    public function forgotPassword(StudentForgotPasswordRequest $request): JsonResponse
    {
        return $this->studentAuthService->sendResetLink($request->validated());
    }

    public function resetPassword(StudentResetPasswordRequest $request): JsonResponse
    {
        return $this->studentAuthService->resetPassword($request->validated());
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $student = $request->user();
        Log::info('Verifying email for student: ' . $student);
        return $this->studentAuthService->verifyEmail($student);
    }

    public function resendEmailVerification(Request $request): JsonResponse
    {
        $student = $request->user(); // Get the authenticated student
        return $this->studentAuthService->resendEmailVerification($student);
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return ApiResponseHelper::success([], 'Logout successful');
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to logout: ' . $e->getMessage(), 500);
        }
    }

    public function getProfile(Request $request): JsonResponse
    {
        $student = $request->user();
        return ApiResponseHelper::success(new StudentResource($student), 'Student retrieved successfully');
    }

    public function updateProfile(StudentProfileUpdateRequest $request): JsonResponse
    {
        try {
            $student = $request->user();
            $data = $request->validated();
            $student->update($data);

            return ApiResponseHelper::success(new StudentResource($student), 'Profile updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update profile: ' . $e->getMessage());
            return ApiResponseHelper::error('Failed to update profile', 500);
        }
    }
}
