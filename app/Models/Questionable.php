<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionable extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'section_id',
        'exam_type_id',
        'exam_sub_type_id',
        'group_id',
        'level_id',
        'subject_id',
        'lesson_id',
        'topic_id',
        'sub_topic_id'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Add relationships for other models as needed
}
