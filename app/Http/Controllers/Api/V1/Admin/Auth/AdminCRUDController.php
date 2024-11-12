<?php
namespace App\Http\Controllers\Api\V1\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseHelper;
use App\Http\Resources\Admin\AdminResource;
use Exception;

class AdminCRUDController extends Controller
{
    /**
     * Get Admin by ID
     */
    public function getAdminByID($id): JsonResponse
    {
        try {
            $admin = Admin::findOrFail($id);
            return ApiResponseHelper::success(new AdminResource($admin), 'Admin retrieved successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Admin not found: ' . $e->getMessage(), 404);
        }
    }

    /**
     * Get All Admins
     */
    public function getAllAdmins(): JsonResponse
    {
        try {
            $admins = Admin::all();
            return ApiResponseHelper::success(AdminResource::collection($admins), 'All admins retrieved successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve admins: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Edit Admin
     */
    public function editAdmin(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $id,
            'password' => 'nullable|string|min:8',
            'email_verified_at' => 'nullable|date',
            'active_status' => 'boolean',
            'role' => 'required|string',  // Validate role
        ]);

        try {
            $admin = Admin::findOrFail($id);

            // Update fields
            $admin->name = $request->name;
            $admin->email = $request->email;

            // Only hash and update the password if provided
            if ($request->filled('password')) {
                $admin->password = bcrypt($request->password);
            }

            $admin->email_verified_at = $request->email_verified_at;
            $admin->active_status = $request->active_status;

            // Update role
            $admin->syncRoles([$request->role]);

            $admin->save();

            return ApiResponseHelper::success(new AdminResource($admin), 'Admin updated successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to update admin: ' . $e->getMessage(), 500);
        }
    }



    /**
     * Delete Admin
     */
    public function deleteAdmin($id): JsonResponse
    {
        try {
            $admin = Admin::findOrFail($id);
            $admin->delete();
            return ApiResponseHelper::success(null, 'Admin deleted successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to delete admin: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Enable or Disable Admin by active_status
     */
    public function toggleAdminStatus($id): JsonResponse
    {
        try {
            $admin = Admin::findOrFail($id);
            $admin->active_status = !$admin->active_status;
            $admin->save();

            $status = $admin->active_status ? 'enabled' : 'disabled';
            return ApiResponseHelper::success(new AdminResource($admin), "Admin $status successfully");
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to update admin status: ' . $e->getMessage(), 500);
        }
    }
}
