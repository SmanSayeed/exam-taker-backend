<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class StudentPdfResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Initialize the array to return
        $data = [
            'id' => $this->id,
            'title' => $this->title,
        ];

        // Check if the student is authenticated and add 'is_subscribed'
        if (Auth::guard('student-api')->check()) {
            $student = Auth::guard('student-api')->user();
            $isSubscribed = $student->pdfSubscriptions()
                ->where('pdf_id', $this->id)
                ->where('is_active', true)
                ->where('expires_at', '>', now()) // Check if subscription is not expired
                ->exists();

            $data['is_subscribed'] = $isSubscribed;

            // If the student is subscribed, add the file details
            if ($isSubscribed) {
                $data = array_merge($data, [
                    'file_path' => $this->file_path,
                    'mime_type' => $this->mime_type,
                    'size' => $this->size,
                    'description' => $this->description,
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
                ]);
            }
        }

        return $data;
    }
}
