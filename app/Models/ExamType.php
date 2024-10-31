<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    use HasFactory;

    protected $fillable = ['section_id', 'title', 'details', 'image', 'status', 'year'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function examSubTypes()
    {
        return $this->hasMany(ExamSubType::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function questionable()
    {
        return $this->hasMany(Questionable::class, 'exam_type_id');
    }
}
