<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreativeQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'creative_question_text',
        'creative_question_type', //a,b,c,d
        'creative_question_description',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Add any additional methods or relationships if necessary
}
