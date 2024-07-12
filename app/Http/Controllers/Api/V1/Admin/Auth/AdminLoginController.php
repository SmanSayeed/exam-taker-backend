<?php

namespace App\Http\Controllers\Api\V1\Admin\Auth;

use App\DTOs\AdminDTO\AdminLoginData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Services\Admin\AdminAuthService;
use Illuminate\Http\JsonResponse;
use Exception;

class AdminLoginController extends Controller
{
    protected AdminAuthService $adminAuthService;

    public function __construct(AdminAuthService $adminAuthService)
    {
        $this->adminAuthService = $adminAuthService;
    }

    public function login(AdminLoginRequest $request): JsonResponse
    {
        try {
            $loginData = AdminLoginData::from($request->validated());
            return $this->adminAuthService->authenticate($loginData);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to login: ' . $e->getMessage()], 500);
        }
    }
}
