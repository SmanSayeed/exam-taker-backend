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

    //  public function examTypesIfQuestionExists()
    // {
    //     // Modify this function to ensure it filters based on the existence of related Questionable entries
    //     return $this->hasMany(ExamType::class)->whereHas('questionable', function ($query) {
    //         $query->whereNotNull('question_id');
    //     });
    // }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }


}
