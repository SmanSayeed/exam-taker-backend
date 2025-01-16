<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'examination_id',
        'student_id',
        'question_id',
        'question_type',
        'is_answer_submitted',
        'is_exam_time_out',
        'mcq_answers',
        'creative_answers',
        'normal_answers',
        'obtained_mark',
        'exam_start_time',
        'submission_time',
        'is_second_timer',
        'status',
    ];

    protected $casts = [
        'mcq_answers' => 'array',
        'creative_answers' => 'array',
        'normal_answers' => 'array',
    ];

    // Relationship to examinations
    public function examination()
    {
        return $this->belongsTo(Examination::class, 'examination_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    // Add this relationship
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
