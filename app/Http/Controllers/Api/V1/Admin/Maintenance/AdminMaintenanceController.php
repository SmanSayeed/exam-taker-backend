<?php
namespace App\Http\Controllers\Api\V1\Admin\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

class AdminMaintenanceController extends Controller
{
    public function clearCache(): JsonResponse
    {
        try {
            // Clear caches
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('optimize:clear');
            return response()->json(['status' => 'success', 'message' => 'Cache cleared and optimized successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to clear cache and optimize: ' . $e->getMessage()], 500);
        }
    }

    public function optimize(): JsonResponse
    {
        try {
            // Optimize
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            Artisan::call('optimize');

            return response()->json(['status' => 'success', 'message' => 'Cache cleared and optimized successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to clear cache and optimize: ' . $e->getMessage()], 500);
        }
    }
}
