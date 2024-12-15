<?php

namespace App\Http\Controllers\Api\V1\Student\StudentPayment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseHelper;
use App\Http\Resources\StudentPaymentStudentResource;
use App\Models\StudentPayment;
use Illuminate\Support\Facades\Auth;

class StudentPaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $studentPayments = StudentPayment::where('student_id', Auth::id())->paginate($perPage);
        return ApiResponseHelper::success(StudentPaymentStudentResource::collection($studentPayments), 'Transactions retrieved successfully');
    }

    public function show(StudentPayment  $student_payment): JsonResponse
    {
        if ($student_payment->student_id !== Auth::id()) {
            return ApiResponseHelper::error('Unauthorized access to this transaction', 403);
        }

        return ApiResponseHelper::success(
            new StudentPaymentStudentResource($student_payment),
            'Transaction retrieved successfully'
        );
    }
}
