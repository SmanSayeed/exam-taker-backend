<?php

namespace App\Http\Controllers\Api\V1\Student\PdfSubscription;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PdfPaymentRequest;
use App\Http\Resources\PdfResource;
use App\Http\Resources\PdfSubscriptionStudentResource;
use App\Models\Pdf;
use App\Models\PdfSubscriptionPayment;
use App\Models\PdfSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PdfSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);

        // Ensure only subscriptions for the authenticated user are retrieved
        $subscriptions = PdfSubscription::where('student_id', Auth::id())->paginate($perPage);

        return ApiResponseHelper::success(PdfSubscriptionStudentResource::collection($subscriptions), 'Subscriptions retrieved successfully');
    }

    public function show(PdfSubscription $subscription)
    {
        Log::info(Auth::id());
        if ($subscription->student_id !== Auth::id()) {
            return ApiResponseHelper::error('Unauthorized access to this subscription', 403);
        }

        return ApiResponseHelper::success(
            new PdfSubscriptionStudentResource($subscription),
            'PdfSubscription retrieved successfully'
        );
    }


    public function pay(PdfPaymentRequest $request, pdf $pdf)
    {
        DB::beginTransaction();
        try {
            // Create a payment record, storing the pdf_id and amount explicitly
            $payment = PdfSubscriptionPayment::create([
                'student_id'       => Auth::id(),
                'pdf_id'       => $pdf->id,
                'payment_method'   => $request->payment_method,  // e.g., bkash, nagad
                'mobile_number'    => $request->mobile_number,
                'transaction_id'   => $request->transaction_id,
                'amount'           => $request->amount,          // Payment amount
                'coupon'           => $request->coupon,
            ]);

            DB::commit();

            return ApiResponseHelper::success([
                'payment' => [
                    'pdf_id'      => $payment->pdf_id,
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



    //get all subscribed pdfs
    public function getSubscribedPdfs()
    {
        // Retrieve all pdfs the authenticated student has subscribed to
        $pdfs = PdfSubscription::where('student_id', Auth::id())
            ->with('pdf') // Eager load the pdf relationship
            ->get()
            ->pluck('pdf') // Extract only the pdf from each subscription
            ->unique(); // Ensure no duplicate pdfs are returned

        // Return as a collection of PdfResource
        return ApiResponseHelper::success(PdfResource::collection($pdfs), 'Subscribed pdfs retrieved successfully');
    }
}
