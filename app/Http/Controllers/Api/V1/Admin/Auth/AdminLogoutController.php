<?php

namespace App\Http\Controllers\Api\V1\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponseHelper;
class AdminLogoutController extends Controller
{
    public function logout(Request $request)
    {
        try {
            // Get the currently authenticated admin
            $admin = Auth::guard('sanctum')->user();

            // Revoke the token that was used to authenticate the current request
            $admin->currentAccessToken()->delete();

            return ApiResponseHelper::success([], 'Logout successful');
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to logout: ' . $e->getMessage(), 500);
        }
    }
}
