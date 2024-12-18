<?php

namespace App\Http\Controllers\Api\V1\Student\StudentPayment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseHelper;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\StudentPaymentStudentResource;
use App\Models\StudentPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentPaymentController extends Controller
{
    public function pay(PaymentRequest $request)
    {
        DB::beginTransaction();
        try {
            // Create a payment record, storing the package_id and amount explicitly
            $payment = StudentPayment::create([
                'student_id'       => Auth::id(),
                'resource_id' => $request->resource_id,
                'resource_type'    => $request->resource_type,
                'payment_method'   => $request->payment_method,  // e.g., bkash, nagad
                'mobile_number'    => $request->mobile_number,
                'transaction_id'   => $request->transaction_id,
                'amount'           => $request->amount,          // Payment amount
                'coupon'           => $request->coupon,
            ]);

            DB::commit();

            return ApiResponseHelper::success([
                'payment' => [
                    'id'              => $payment->id,
                    'student_id'      => $payment->student_id,
                    'resource_id'     => $payment->resource_id,
                    'resource_type'   => $payment->resource_type,
                    'payment_method'  => $payment->payment_method,
                    'mobile_number'   => $payment->mobile_number,
                    'transaction_id'  => $payment->transaction_id,
                    'amount'          => $payment->amount,
                    'coupon'          => $payment->coupon,
                ],
            ], 'Payment processed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Payment failed: ' . $e->getMessage(), 500);
        }
    }
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
