<?php

namespace App\Http\Controllers\Api\V1\Student\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseHelper;
use App\Http\Resources\StudentPaymentResource;
use App\Models\StudentPayment;
use Illuminate\Support\Facades\Auth;

class StudentPaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $studentPayments = StudentPayment::where('student_id', Auth::id())->paginate($perPage);
        return ApiResponseHelper::success(StudentPaymentResource::collection($studentPayments), 'Transactions retrieved successfully');
    }

    public function show(StudentPayment $transaction)
    {
        return ApiResponseHelper::success(
            new StudentPaymentResource($transaction),
            'Transaction retrieved successfully'
        );
    }
}
