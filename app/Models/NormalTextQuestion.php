<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NormalTextQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'normal_question_description',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Add any additional methods or relationships if necessary
}
