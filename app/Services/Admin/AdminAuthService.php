<?php

namespace App\Services\Admin;

use App\DTOs\Admin\Auth\AdminRegistrationData;
use App\DTOs\Admin\Auth\AdminLoginData;
use App\Helpers\ApiResponseHelper;
use App\Repositories\Admin\AdminRepositoryInterface;
use App\Http\Resources\Admin\AdminResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class AdminAuthService
{
    protected AdminRepositoryInterface $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }
    public function createAdmin(AdminRegistrationData $adminRegistrationData): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $adminRegistrationData->toArray();
            $data['password'] = bcrypt($adminRegistrationData->password);

            $admin = $this->adminRepository->create($data);

            DB::commit();

            return ApiResponseHelper::success(new AdminResource($admin), 'Admin created successfully', 201);
        } catch (Exception $e) {
            DB::rollback();

            return ApiResponseHelper::error('Failed to create admin: ' . $e->getMessage(), 500);
        }
    }

    public function authenticate(AdminLoginData $adminLoginData): JsonResponse
    {
        try {
            $credentials = $adminLoginData->toArray();
            if (!Auth::attempt($credentials)) {
                return ApiResponseHelper::error('Invalid credentials', 401);
            }

            $admin = Auth::user();
            $token = $admin->createToken('API Token')->plainTextToken;
            return ApiResponseHelper::success([
                'token' => $token,
                'admin' => new AdminResource($admin)
            ], 'Login successful');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to authenticate: ' . $e->getMessage(), 500);
        }
    }

    public function findAdminByEmail(string $email): JsonResponse
    {
        try {
            $admin = $this->adminRepository->findByEmail($email);
            if ($admin) {
                return ApiResponseHelper::success(new AdminResource($admin), 'Admin found');
            } else {
                return ApiResponseHelper::error('Admin not found', 404);
            }
        } catch (ModelNotFoundException $e) {
            return ApiResponseHelper::error('Admin not found', 404);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to find admin: ' . $e->getMessage(), 500);
        }
    }



    public function sendPasswordResetLink(string $email): JsonResponse
    {
        try {
            // Simulating success of sending password reset link
            return ApiResponseHelper::success([], 'Password reset link sent');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to send password reset link: ' . $e->getMessage(), 500);
        }
    }
}
