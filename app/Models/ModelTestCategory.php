<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelTestCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_test_id',
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

    public function modelTest()
    {
        return $this->belongsTo(ModelTest::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    public function examSubType()
    {
        return $this->belongsTo(ExamSubType::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function subTopic()
    {
        return $this->belongsTo(SubTopic::class);
    }
}
