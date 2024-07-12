<?php
// app/Http/Controllers/Api/V1/Admin/Auth/AdminProfileController.php

namespace App\Http\Controllers\Api\V1\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\Admin\AdminAuthService;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseHelper;
use App\Http\Resources\Admin\AdminResource;
use Exception;

class AdminProfileController extends Controller
{
    protected AdminAuthService $adminAuthService;

    public function __construct(AdminAuthService $adminAuthService)
    {
        $this->adminAuthService = $adminAuthService;
    }

    public function getAdminProfile(): JsonResponse
    {
        try {
            $admin = auth()->guard('sanctum')->user();

            return ApiResponseHelper::success(new AdminResource($admin), 'Admin profile retrieved successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to get admin profile: ' . $e->getMessage(), 500);
        }
    }
}
