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

/**
 * @OA\Swagger(
 * schemes={"https"},
 * host="mywebsite.com",
 * basePath="http://localhost:8000/api/v1",
 * @OA\Info(
 * version="1.0.0",
 * title="My Website",
 * description="Put Markdown Here [a Link](https://www.google.com)",
 * @OA\Contact(
 * email="my@email"
 *      ),
 *   ),
 * )
 */

class AdminProfileController extends Controller
{

    protected AdminAuthService $adminAuthService;

    public function __construct(AdminAuthService $adminAuthService)
    {
        $this->adminAuthService = $adminAuthService;
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
