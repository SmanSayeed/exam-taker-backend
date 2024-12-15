<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PackageStudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'duration_days' => $this->duration_days,
            'img' => $this->img ? asset('storage/' . $this->img) : null,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];

        // Check if student is authenticated and add is_subscribed
        if (Auth::guard('student-api')->check()) {
            $student = Auth::guard('student-api')->user();
            $data['is_subscribed'] = $student->subscriptions()
                ->where('package_id', $this->id)
                ->where('is_active', true)
                ->where('expires_at', '>', now()) // Check if not expired
                ->exists();
        }

        return $data;
    }
}
