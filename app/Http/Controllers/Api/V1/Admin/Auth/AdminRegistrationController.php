<?php

namespace App\Http\Controllers\Api\V1\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRegistrationRequest;
use App\Services\Admin\AdminAuthService;
use App\DTOs\Admin\Auth\AdminRegistrationData;
use Illuminate\Http\JsonResponse;
use Exception;

class AdminRegistrationController extends Controller
{
    protected AdminAuthService $adminAuthService;

    public function __construct(AdminAuthService $adminAuthService)
    {
        $this->adminAuthService = $adminAuthService;
    }

    public function store(AdminRegistrationRequest $request): JsonResponse
    {
        try {
            $adminData = AdminRegistrationData::from($request->validated());
            return $this->adminAuthService->createAdmin($adminData);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to register admin: ' . $e->getMessage()], 500);
        }
    }
}
