<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PdfSubscriptionStudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'is_active' => $this->is_active,
            'subscribed_at' => $this->subscribed_at,
            'expires_at' => $this->expires_at,
            'pdf' => [
                'id' => $this->pdf->id,
                'title' => $this->pdf->title,
            ],
        ];
    }
}
