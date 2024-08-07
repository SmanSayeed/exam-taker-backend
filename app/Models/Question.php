<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'images',
        'is_paid',
        'is_featured',
        'type',
        'mark',
        'status'
    ];

    public function mcqQuestions()
    {
        return $this->hasMany(McqQuestion::class);
    }

    public function creativeQuestions()
    {
        return $this->hasMany(CreativeQuestion::class);
    }

    public function sections()
    {
        return $this->belongsTo(Section::class, 'questionable', 'question_id', 'section_id');
    }

    public function examTypes()
    {
        return $this->belongsTo(ExamType::class, 'questionable', 'question_id', 'exam_type_id');
    }

    public function examSubTypes()
    {
        return $this->belongsTo(ExamSubType::class, 'questionable', 'question_id', 'exam_sub_type_id');
    }

    public function groups()
    {
        return $this->belongsTo(Group::class, 'questionable', 'question_id', 'group_id');
    }

    public function levels()
    {
        return $this->belongsTo(Level::class, 'questionable', 'question_id', 'level_id');
    }

    public function subjects()
    {
        return $this->belongsTo(Subject::class, 'questionable', 'question_id', 'subject_id');
    }

    public function lessons()
    {
        return $this->belongsTo(Lesson::class, 'questionable', 'question_id', 'lesson_id');
    }

    public function topics()
    {
        return $this->belongsTo(Topic::class, 'questionable', 'question_id', 'topic_id');
    }

    public function subTopics()
    {
        return $this->belongsTo(SubTopic::class, 'questionable', 'question_id', 'sub_topic_id');
    }

    public function attachable()
    {
        return $this->hasOne(Questionable::class);
    }

    // Add any relationships or additional methods if necessary
}
