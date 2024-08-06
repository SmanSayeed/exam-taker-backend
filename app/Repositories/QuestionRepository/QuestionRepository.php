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
            $query->where('type', $type);
        }

        return $query->with(['mcqQuestions', 'creativeQuestions'])->paginate($perPage);
    }
}
