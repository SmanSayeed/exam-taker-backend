<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelTest extends Model
{

    protected $fillable = [
        'package_id',    // Foreign key to the package
        'title',         // Title of the model test
        'description',   // Description of the model test
        'start_time',    // Start time of the model test
        'end_time',      // End time of the model test
        'is_active'      // Indicates if the test is active
    ];

    use HasFactory;

    public function modelTestCategory()
    {
        return $this->hasOne(ModelTestCategory::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    // Accessing the related models through ModelTestCategory
    public function section()
    {
        return $this->hasOneThrough(Section::class, ModelTestCategory::class, 'model_test_id', 'id', 'id', 'section_id');
    }

    public function examType()
    {
        return $this->hasOneThrough(ExamType::class, ModelTestCategory::class, 'model_test_id', 'id', 'id', 'exam_type_id');
    }

    public function examSubType()
    {
        return $this->hasOneThrough(ExamSubType::class, ModelTestCategory::class, 'model_test_id', 'id', 'id', 'exam_sub_type_id');
    }

    public function group()
    {
        return $this->hasOneThrough(Group::class, ModelTestCategory::class, 'model_test_id', 'id', 'id', 'group_id');
    }

    public function level()
    {
        return $this->hasOneThrough(Level::class, ModelTestCategory::class, 'model_test_id', 'id', 'id', 'level_id');
    }

    public function subject()
    {
        return $this->hasOneThrough(Subject::class, ModelTestCategory::class, 'model_test_id', 'id', 'id', 'subject_id');
    }

    public function lesson()
    {
        return $this->hasOneThrough(Lesson::class, ModelTestCategory::class, 'model_test_id', 'id', 'id', 'lesson_id');
    }

    public function topic()
    {
        return $this->hasOneThrough(Topic::class, ModelTestCategory::class, 'model_test_id', 'id', 'id', 'topic_id');
    }

    public function subTopic()
    {
        return $this->hasOneThrough(SubTopic::class, ModelTestCategory::class, 'model_test_id', 'id', 'id', 'sub_topic_id');
    }
}
