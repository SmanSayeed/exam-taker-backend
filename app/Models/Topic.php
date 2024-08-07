<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = ['lesson_id', 'title', 'description', 'image'];


    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function subTopics()
    {
        return $this->hasMany(SubTopic::class);
    }
}
