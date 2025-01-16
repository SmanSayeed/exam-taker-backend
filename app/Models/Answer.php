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
        'type',
        'is_answer_submitted',
        'is_exam_time_out',
        'mcq_answers',
        'creative_answers',
        'normal_answers',
        'total_marks',
        'correct_count',
        'total_questions_count',
        'exam_start_time',
        'submission_time',
        'is_second_timer',
        'status',
        'comments',
        'is_reviewed'  // Add this
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
