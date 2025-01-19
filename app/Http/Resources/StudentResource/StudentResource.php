<?php

namespace App\Http\Resources\StudentResource;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public static function collection($resource)
    {
        return [
            'data' => $resource->map(function ($item) {
                return new static($item);
            }),
            'pagination' => [
                'per_page' => $resource->perPage(),
                'total_pages' => $resource->lastPage(),
                'current_page' => $resource->currentPage(),
                'prev_page' => $resource->previousPageUrl(),
                'next_page' => $resource->nextPageUrl(),
                'total_data' => $resource->total(),
            ],
        ];
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'section_id' => $this->section_id,
            'profile_image' => $this->profile_image,
            'ip_address' => $this->ip_address,
            'country' => $this->country,
            'country_code' => $this->country_code,
            'address' => $this->address,
            'active_status' => $this->active_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'exams_count' => $this->exams_count,
            'paid_exam_quota' => $this->paid_exam_quota,
        ];
    }
}
