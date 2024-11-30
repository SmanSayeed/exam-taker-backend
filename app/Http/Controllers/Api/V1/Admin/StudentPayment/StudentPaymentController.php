<?php

namespace App\Http\Controllers\Api\V1\Admin\StudentPayment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseHelper;
use App\Http\Resources\StudentPaymentResource;
use App\Http\Resources\TransactionResource;
use App\Models\StudentPayment;

class StudentPaymentController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $studentPayments = StudentPayment::paginate($perPage);
        return ApiResponseHelper::success(StudentPaymentResource::collection($studentPayments), 'Transactions retrieved successfully');
    }

    public function show(StudentPayment $student_payment): JsonResponse
    {
        return ApiResponseHelper::success(
            new StudentPaymentResource($student_payment),
            'Transaction retrieved successfully'
        );
    }

    public function destroy(StudentPayment $student_payment): JsonResponse
    {
        // Check if the payment is verified
        if ($student_payment->verified) {
            return ApiResponseHelper::error('Verified payments cannot be deleted', 400);
        }

        // Proceed with deletion if not verified
        $student_payment->delete();
        return ApiResponseHelper::success(null, 'Transaction deleted successfully');
    }
}
