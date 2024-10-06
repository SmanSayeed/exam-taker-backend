<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelTestCategoryResource extends JsonResource
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
            'section' => $this->whenLoaded('section'),  // Loads related section
            'exam_type' => $this->whenLoaded('examType'), // Loads related exam type
            'group' => $this->whenLoaded('group'), // Loads related group
            'level' => $this->whenLoaded('level'), // Loads related level
            'subject' => $this->whenLoaded('subject'), // Loads related subject
            'lesson' => $this->whenLoaded('lesson'), // Loads related lesson
            'topic' => $this->whenLoaded('topic'), // Loads related topic
            'sub_topic' => $this->whenLoaded('subTopic'), // Loads related sub topic
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
