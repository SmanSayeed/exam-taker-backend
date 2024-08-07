<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSubType extends Model
{
    use HasFactory;

    protected $fillable = ['exam_type_id', 'title', 'details', 'image', 'status'];

    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
