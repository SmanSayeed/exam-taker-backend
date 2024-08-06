<?php

namespace App\Repositories\QuestionRepository;

use App\Models\CreativeQuestion;
use Illuminate\Pagination\LengthAwarePaginator;


class CreativeQuestionRepository extends QueBaseRepository
{
    public function __construct()
    {
        parent::__construct(new CreativeQuestion());
    }

    public function findWithDetails(int $id)
    {
        return CreativeQuestion::with('question')->findOrFail($id);
    }

    public function getAllWithPagination(int $perPage): LengthAwarePaginator
    {
        return CreativeQuestion::with('question')->paginate($perPage);
    }
}
