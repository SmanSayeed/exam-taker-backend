<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminPdfResource extends JsonResource
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
            'title' => $this->title,
            'storage_url' => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'file_link' => $this->file_link ?? null,
            'mime_type' => $this->mime_type,
            'img' => $this->img ? asset('storage/' . $this->img) : null,
            'size' => $this->size,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
