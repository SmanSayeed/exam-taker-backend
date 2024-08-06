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

    // Add any relationships or additional methods if necessary
}
