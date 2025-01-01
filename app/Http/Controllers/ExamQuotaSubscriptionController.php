<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Models\ExamQuotaSubscription;
use App\Models\Student;
use Illuminate\Http\Request;

class ExamQuotaSubscriptionController extends Controller
{


    public function submitSubscriptionRequest(Request $request)
{
    try {
        // Validate request data
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'mobile_number' => 'required|string|max:15',
            'payment_method' => 'required|string|in:Bkash,Nagad,Rocket,Card',
            'transaction_id' => 'required|string|max:50|unique:exam_quota_subscriptions,transaction_id',
            'coupon' => 'nullable|string|max:50',
        ]);

        // Check if the student has an active subscription
        $student = Student::findOrFail($validatedData['student_id']);
        $existingSubscription = ExamQuotaSubscription::where('student_id', $validatedData['student_id'])
            ->where('verified', true) // Check only verified subscriptions
            ->first();

        if ($existingSubscription && $student->paid_exam_quota > $student->exams_count) {
            return ApiResponseHelper::error(
                'You already have an active subscription and your quota is not over.',
                400
            );
        }

        // Create a new subscription record
        $subscription = ExamQuotaSubscription::create([
            'student_id' => $validatedData['student_id'],
            'mobile_number' => $validatedData['mobile_number'],
            'payment_method' => $validatedData['payment_method'],
            'transaction_id' => $validatedData['transaction_id'],
            'coupon' => $validatedData['coupon'],
            'verified' => false, // Default to not verified
            'verified_at' => null,
        ]);

        return ApiResponseHelper::success('Subscription request submitted successfully', $subscription);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return ApiResponseHelper::error('Validation failed', 422, $e->errors());
    } catch (\Exception $e) {
        \Log::error('Error submitting subscription request: ' . $e->getMessage());
        return ApiResponseHelper::error('Failed to submit subscription request', 500);
    }
}

    public function getFilteredSubscriptions(Request $request)
    {
        try {
            // Retrieve filter inputs
            $studentName = $request->input('student_name');
            $studentId = $request->input('student_id');
            $phoneNumber = $request->input('phone_number');
            $verified = $request->input('verified'); // Accepts true/false

            // Build the query
            $query = ExamQuotaSubscription::query()->with('student');

            // Apply filters
            if (!is_null($studentName)) {
                $query->whereHas('student', function ($query) use ($studentName) {
                    $query->where('name', 'like', '%' . $studentName . '%');
                });
            }

            if (!is_null($studentId)) {
                $query->where('student_id', $studentId);
            }

            if (!is_null($phoneNumber)) {
                $query->where('mobile_number', 'like', '%' . $phoneNumber . '%');
            }

            if (!is_null($verified)) {
                $query->where('verified', filter_var($verified, FILTER_VALIDATE_BOOLEAN));
            }

            // Get the filtered subscriptions
            $subscriptions = $query->get();

            return ApiResponseHelper::success('Filtered subscriptions fetched successfully', $subscriptions);
        } catch (\Exception $e) {
            \Log::error('Error fetching filtered subscriptions: ' . $e->getMessage());
            return ApiResponseHelper::error('Failed to fetch filtered subscriptions', 500);
        }
    }


public function getPendingSubscriptions()
{
    $subscriptions = ExamQuotaSubscription::where('verified', false)->with('student')->get();

    return ApiResponseHelper::success('Pending subscriptions fetched successfully', $subscriptions);
}

public function verifySubscription(Request $request, $id)
{
    $paid_exam_quota = 100;
    try {
        $subscription = ExamQuotaSubscription::findOrFail($id);

        if ($subscription->verified) {
            return ApiResponseHelper::error('This subscription is already verified', 400);
        }

        $subscription->update([
            'verified' => true,
            'verified_at' => now(),
        ]);

        $student = Student::findOrFail($subscription->student_id);
        $student->update(['paid_exam_quota' => $paid_exam_quota]); // Automatically set quota to 100

        return ApiResponseHelper::success('Subscription verified and quota updated', [
            'subscription' => $subscription,
            'student' => $student,
        ]);
    } catch (\Exception $e) {
        \Log::error('Error verifying subscription: ' . $e->getMessage());
        return ApiResponseHelper::error('Failed to verify subscription', 500);
    }
}


public function updateStudentQuota(Request $request, $studentId)
{
    try {
        // Validate the request data
        $validatedData = $request->validate([
            'paid_exam_quota' => 'required|integer|min:0',
            'exams_count' => 'required|integer|min:0',
        ]);

        // Find the student
        $student = Student::findOrFail($studentId);

        // Update the fields
        $student->update([
            'paid_exam_quota' => $validatedData['paid_exam_quota'],
            'exams_count' => $validatedData['exams_count'],
        ]);

        // Return a success response
        return ApiResponseHelper::success(
            'Student quota and exam count updated successfully',
            ['student' => $student]
        );
    } catch (\Illuminate\Validation\ValidationException $e) {
        return ApiResponseHelper::error('Validation failed', 422, $e->errors());
    } catch (\Exception $e) {
        \Log::error('Error updating student quota: ' . $e->getMessage());
        return ApiResponseHelper::error('Failed to update student quota', 500);
    }
}

}
