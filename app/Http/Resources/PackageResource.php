<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isAdminRoute = $request->is('admin/*');

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'duration_days' => $this->duration_days,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];

        if ($isAdminRoute) {
            $data['is_active'] = $this->is_active;
            $data['category'] = new PackageCategoryResource($this->categories);
        }

        // Add is_subscribed if the student is authenticated
        if (isset($this->is_subscribed)) {
            $data['is_subscribed'] = $this->is_subscribed;
        }

        return $data;
    }
}
