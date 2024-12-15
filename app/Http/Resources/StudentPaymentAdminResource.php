<?php

namespace App\Http\Resources;

use App\Http\Resources\StudentResource\StudentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentPaymentAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array for admin routes.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_method' => $this->payment_method,
            'mobile_number' => $this->mobile_number,
            'transaction_id' => $this->transaction_id,
            'amount' => $this->amount,
            'verified' => $this->verified,
            'verified_at' => $this->verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'student_id' => $this->student_id,
            'subscription_id' => $this->subscription_id,
            'package_id' => $this->package_id,
        ];
    }
}
