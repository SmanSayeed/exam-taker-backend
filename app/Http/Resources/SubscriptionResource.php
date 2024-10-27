<?php

namespace App\Http\Resources;

use App\Http\Resources\StudentResource\StudentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Check if the current route contains the 'admin' segment
        $isAdminRoute = strpos($request->getRequestUri(), '/admin') !== false;

        Log::debug('isAdminRoute: ' . $isAdminRoute);

        $data = [
            'id' => $this->id,
            'is_active' => $this->is_active,
            'subscribed_at' => $this->subscribed_at,
            'expires_at' => $this->expires_at,
            'package' => new PackageResource($this->package),
        ];

        // Include student data if in admin route
        if ($isAdminRoute) {
            $data['student'] = new StudentResource($this->student);
        }

        return $data;
    }
}
