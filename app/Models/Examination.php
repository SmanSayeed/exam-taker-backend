<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'is_paid',
        'created_by',
        'created_by_role',
        'start_time',
        'end_time',
        'student_ended_at',
        'time_limit',
        'is_negative_mark_applicable',
        'section_id',
        'exam_type_id',
        'exam_sub_type_id',
        'group_id',
        'level_id',
        'subject_id',
        'lesson_id',
        'topic_id',
        'sub_topic_id',
        'questions',
    ];

    protected $casts = [
        'section_categories' => 'array',
        'subject_categories' => 'array',
        'questions' => 'array',
    ];

    // Relationship to answers
    public function answers()
    {
        return $this->hasMany(Answer::class, 'examination_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'created_by');
    }
}
