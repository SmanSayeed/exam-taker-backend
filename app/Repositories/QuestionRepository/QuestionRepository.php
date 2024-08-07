<?php

namespace App\Repositories\QuestionRepository;

use App\Models\Question;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionRepository extends QueBaseRepository
{
    public function __construct(Question $model)
    {
        parent::__construct($model);
    }

    public function getNormalQuestionWithPagination(int $perPage): LengthAwarePaginator
    {
        return Question::where('type', 'normal')->paginate($perPage);
    }

    public function getQuestionsWithTypes(?string $type, int $perPage): LengthAwarePaginator
    {
        $query = Question::query();

        if ($type) {
            $query =  $query->where('type', $type);
        }

        switch($type){
            case "mcq":
                $query =  $query->with(['mcqQuestions']);
                break;
            case "creative":
                $query =  $query->with(['creativeQuestions']);
                break;
            case "normal":
                // keep at it is
                break;
            default:
                $query =  $query->with(['creativeQuestions','mcqQuestions']);
                break;

        }

        $query = $query->with('attachable')->orderBy('created_at', 'desc')->paginate($perPage);

        return $query;


    }


}
