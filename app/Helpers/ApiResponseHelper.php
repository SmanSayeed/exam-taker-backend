<?php
// app/Helpers/ApiResponseHelper.php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponseHelper
{
    public static function success($data = [], $message = 'Operation successful', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], (int)$status);
    }

    public static function error($message = 'Operation failed', int $status = 500, $errors = []): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], (int)$status);
    }
}
