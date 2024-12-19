<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageAdminResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'duration_days' => $this->duration_days,
            'img' => $this->img ? asset('storage/' . $this->img) : null,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'is_active' => $this->is_active,
            'tags' => TagResource::collection($this->tags),
            'category' => new PackageCategoryResource($this->packageCategory),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
