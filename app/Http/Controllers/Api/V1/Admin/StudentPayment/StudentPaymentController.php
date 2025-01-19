<?php

namespace App\Http\Controllers\Api\V1\Admin\StudentPayment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseHelper;
use App\Http\Resources\StudentPaymentAdminResource;
use App\Models\Admin;
use App\Models\Package;
use App\Models\StudentPayment;

class StudentPaymentController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        // Retrieve 'per_page' value from the request, defaulting to 300
        $perPage = $request->get('per_page', 300);

        // Query the StudentPayment model with student relationship
        $studentPayments = StudentPayment::with('student')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

$package = new Package();

        // Format the data manually
        $data = $studentPayments->map(function ($payment) {
if($payment->resource_type === 'package'){
   if($payment->resource_id){
    $package = Package::find($payment->resource_id);
   }
}

            return [
                'id' => $payment->id,
                'payment_method' => $payment->payment_method,
                'mobile_number' => $payment->mobile_number,
                'transaction_id' => $payment->transaction_id,
                'amount' => $payment->amount,
                'verified' => $payment->verified,
                'verified_at' => $payment->verified_at,
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at,
                'student_id' => $payment->student_id,
                'resource_type' => $payment->resource_type,
                'resource_id' => $payment->resource_id,
                'package' => $payment->resource_type === 'package' ? ['id' => $payment->resource_id] : null,
                'student' => $payment->student ? [
                    'id' => $payment->student->id,
                    'name' => $payment->student->name,
                    'email' => $payment->student->email,
                    'phone' => $payment->student->phone,
                    // Add any other student fields you need
                ] : null,
                'package_details'=>$package
            ];
        });

        // Build the response with pagination details
        return ApiResponseHelper::success([
            'data' => $data,
            'pagination' => [
                'per_page' => $studentPayments->perPage(),
                'total_pages' => $studentPayments->lastPage(),
                'current_page' => $studentPayments->currentPage(),
                'prev_page' => $studentPayments->previousPageUrl(),
                'next_page' => $studentPayments->nextPageUrl(),
                'last_page' => $studentPayments->lastPage(),
            ],
        ], 'Transactions retrieved successfully');
    }

    public function show(StudentPayment $student_payment): JsonResponse
    {
        return ApiResponseHelper::success(
            new StudentPaymentAdminResource($student_payment),
            'Transaction retrieved successfully'
        );
    }

    public function changeStatus(StudentPayment $student_payment): JsonResponse
    {
        $student_payment->verified = !$student_payment->verified;
        $student_payment->save();
        return ApiResponseHelper::success(
            new StudentPaymentAdminResource($student_payment),
            'Transaction status updated successfully'
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
