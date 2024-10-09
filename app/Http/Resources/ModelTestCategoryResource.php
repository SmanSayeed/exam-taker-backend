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
            'section' => $this->section,  // Loads related section
            'exam_type' => $this->examType, // Loads related exam type
            'group' => $this->group, // Loads related group
            'level' => $this->level, // Loads related level
            'subject' => $this->subject, // Loads related subject
            'lesson' => $this->lesson, // Loads related lesson
            'topic' => $this->topic, // Loads related topic
            'sub_topic' => $this->subTopic, // Loads related sub topic
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
