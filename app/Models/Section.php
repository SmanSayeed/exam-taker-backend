<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'details', 'image', 'status'];
    public function examTypes()
    {
        return $this->hasMany(ExamType::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }


}
