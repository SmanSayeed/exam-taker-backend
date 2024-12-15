<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use App\Helpers\ApiResponseHelper;

class StorageController extends Controller
{
    public function linkStorage()
    {
        try {
            // Execute the storage:link command
            Artisan::call('storage:link');

            return ApiResponseHelper::success([], 'Storage linked successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to link storage: ' . $e->getMessage(), 500);
        }
    }
}
