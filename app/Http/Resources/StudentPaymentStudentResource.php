<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentPaymentStudentResource extends JsonResource
{
    /**
     * Transform the resource into an array for student routes.
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
            'coupon' => $this->coupon,
            'verified' => $this->verified,
            'verified_at' => $this->verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'package_id' => $this->package_id,
        ];
    }
}
