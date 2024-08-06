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
        'question_type',
        'mark',
        'status'
    ];

    // Add any relationships or additional methods if necessary
}
