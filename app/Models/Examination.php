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
        'section_categories',
        'subject_categories',
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
