<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelTestQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_test_id',
        'question_id',
    ];
}
