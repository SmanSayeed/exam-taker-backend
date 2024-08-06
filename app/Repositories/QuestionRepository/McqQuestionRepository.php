<?php

namespace App\Repositories\QuestionRepository;

use App\Models\McqQuestion;
use Illuminate\Pagination\LengthAwarePaginator;

class McqQuestionRepository extends QueBaseRepository
{
    public function __construct()
    {
        parent::__construct(new McqQuestion());
    }

    public function findWithDetails(int $id)
    {
        return McqQuestion::with('question')->findOrFail($id);
    }

    public function getAllWithPagination(int $perPage): LengthAwarePaginator
    {
        return McqQuestion::with('question')->paginate($perPage);
    }
}
