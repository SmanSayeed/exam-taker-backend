<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExaminationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'is_paid' => $this->is_paid,
            'created_by' => $this->created_by,
            'created_by_role' => $this->created_by_role,
            'start_time' => $this->start_time->toDateTimeString(),  // Format the timestamp
            'end_time' => $this->end_time ? $this->end_time->toDateTimeString() : null,  // Null check for nullable field
            'student_ended_at' => $this->student_ended_at ? $this->student_ended_at->toDateTimeString() : null,  // Nullable check
            'time_limit' => $this->time_limit,
            'is_negative_mark_applicable' => $this->is_negative_mark_applicable,
            'section_id' => $this->section_id,
            'exam_type_id' => $this->exam_type_id,
            'exam_sub_type_id' => $this->exam_sub_type_id,
            'group_id' => $this->group_id,
            'subject_id' => $this->subject_id,
            'level_id' => $this->level_id,
            'lesson_id' => $this->lesson_id,
            'topic_id' => $this->topic_id,
            'sub_topic_id' => $this->sub_topic_id,
            'questions' => $this->questions,  // JSON field to store question IDs
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
    
}
