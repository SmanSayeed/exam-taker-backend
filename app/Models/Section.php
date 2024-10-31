<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Section extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'details', 'image', 'status'];
    public function examTypes()
    {
        return $this->hasMany(ExamType::class);
    }

    public function examTypesOnlyIfQuestionsExist()
    {
        // Fetch distinct examType IDs from the questionables table where question_id is not null
        $examTypeIdsWithQuestions = DB::table('questionables')
            ->whereNotNull('question_id')
            ->distinct()
            ->pluck('exam_type_id'); // Assuming 'exam_type_id' is the foreign key in 'questionables'

        // Return the examTypes that exist in the fetched IDs
        return $this->examTypes()->whereIn('id', $examTypeIdsWithQuestions);
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }


}
