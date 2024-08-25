<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McqQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'mcq_question_text',
        'is_correct',
        'description',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    // Add any additional methods or relationships if necessary
}
