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
    public function mcqOptions()
    {
        return $this->hasMany(McqQuestion::class, 'question_id');
    }

    // Relationship with CreativeQuestion
    public function creativeOptions()
    {
        return $this->hasMany(CreativeQuestion::class, 'question_id');
    }

    public function questionable()
    {
        return $this->hasOne(Questionable::class);
    }

    // Accessing the related models through Questionable
    public function section()
    {
        return $this->hasOneThrough(Section::class, Questionable::class, 'question_id', 'id', 'id', 'section_id');
    }

    public function examType()
    {
        return $this->hasOneThrough(ExamType::class, Questionable::class, 'question_id', 'id', 'id', 'exam_type_id');
    }

    public function examSubType()
    {
        return $this->hasOneThrough(ExamSubType::class, Questionable::class, 'question_id', 'id', 'id', 'exam_sub_type_id');
    }

    public function group()
    {
        return $this->hasOneThrough(Group::class, Questionable::class, 'question_id', 'id', 'id', 'group_id');
    }

    public function level()
    {
        return $this->hasOneThrough(Level::class, Questionable::class, 'question_id', 'id', 'id', 'level_id');
    }

    public function subject()
    {
        return $this->hasOneThrough(Subject::class, Questionable::class, 'question_id', 'id', 'id', 'subject_id');
    }

    public function lesson()
    {
        return $this->hasOneThrough(Lesson::class, Questionable::class, 'question_id', 'id', 'id', 'lesson_id');
    }

    public function topic()
    {
        return $this->hasOneThrough(Topic::class, Questionable::class, 'question_id', 'id', 'id', 'topic_id');
    }

    public function subTopic()
    {
        return $this->hasOneThrough(SubTopic::class, Questionable::class, 'question_id', 'id', 'id', 'sub_topic_id');
    }

    public function attachable()
    {
        return $this->hasOne(Questionable::class);
    }

    // Add any relationships or additional methods if necessary
}
