<?php

namespace App\Services\Student;

use App\DTOs\StudentDTO\StudentLoginData;
use App\DTOs\StudentDTO\StudentRegistrationData;
use App\Helpers\ApiResponseHelper;
use App\Http\Resources\StudentResource\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class StudentAuthService
{
    public function authenticate(StudentLoginData $studentLoginData): JsonResponse
    {

        try {
            $credentials = $studentLoginData->toArray();

            if($credentials["email"]){
                if(Student::where('email', $credentials["email"])->doesntExist()){
                    return ApiResponseHelper::error('Invalid credentials', 401);
                }
            }
            $student = Auth::guard('student-api')->getProvider()->retrieveByCredentials($credentials);

            if (!$student || !Hash::check($credentials['password'], $student->password)) {
                return ApiResponseHelper::error('Invalid credentials', 401);
            }

            $token = $student->createToken('API Token')->plainTextToken;

            return ApiResponseHelper::success([
                'token' => $token,
                'student' => new StudentResource($student)
            ], 'Login successful');
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to authenticate: ' . $e->getMessage(), 500);
        }
    }

    public function register(StudentRegistrationData $studentRegistrationData): JsonResponse
    {
        try {
            $data = $studentRegistrationData->toArray();
            $data['password'] = Hash::make($studentRegistrationData->password);
            $student = Student::create($data);

            // Optionally, send email verification
            // $student->sendEmailVerificationNotification();

            return ApiResponseHelper::success(new StudentResource($student), 'Student registered successfully', 201);
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to create student: ' . $e->getMessage(), 500);
        }
    }

    public function sendResetLink(array $data): JsonResponse
    {
        $status = Password::broker('students')->sendResetLink($data);

        if ($status === Password::RESET_LINK_SENT) {
            return ApiResponseHelper::success(null, 'Password reset link sent successfully');
        }

        return ApiResponseHelper::error(__($status), 400);
    }

    public function resetPassword(array $data): JsonResponse
    {
        $status = Password::broker('students')->reset(
            $data,
            function (Student $student, $password) {
                $student->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($student));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return ApiResponseHelper::success(null, 'Password reset successfully');
        }

        return ApiResponseHelper::error(__($status), 400);
    }

    public function verifyEmail(Student $student): JsonResponse
    {
        try {
            $student->markEmailAsVerified();
            return ApiResponseHelper::success(null, 'Email verified successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to verify email: ' . $e->getMessage(), 500);
        }
    }

    public function resendEmailVerification(Student $student): JsonResponse
    {
        try {
            $student->sendEmailVerificationNotification();
            return ApiResponseHelper::success(null, 'Verification email resent successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to resend verification email: ' . $e->getMessage(), 500);
        }
    }

    public function logout(Student $student): JsonResponse
    {
        try {
            $student->tokens()->delete();
            return ApiResponseHelper::success(null, 'Logged out successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to logout: ' . $e->getMessage(), 500);
        }
    }
}
