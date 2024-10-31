<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageCategoryResource extends JsonResource
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
            'section_id' => $this->section_id,
            'exam_type_id' => $this->exam_type_id,
            'exam_sub_type_id' => $this->exam_sub_type_id,
        ];
    }
}
