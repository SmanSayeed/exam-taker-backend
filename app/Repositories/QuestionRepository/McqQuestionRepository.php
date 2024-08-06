<?php

namespace App\Repositories\QuestionRepository;

use App\Models\McqQuestion;
use Illuminate\Pagination\LengthAwarePaginator;

class McqQuestionRepository implements QuestionRepositoryInterface
{
    public function create(array $data)
    {
        return McqQuestion::create($data);
    }

    public function update(int $id, array $data)
    {
        $mcqQuestion = McqQuestion::findOrFail($id);
        $mcqQuestion->update($data);
        return $mcqQuestion;
    }

    public function delete(int $id)
    {
        return McqQuestion::destroy($id);
    }

    public function find(int $id)
    {
        return McqQuestion::find($id);
    }

    public function getAll()
    {
        return McqQuestion::all();
    }

    public function changeStatus(int $id)
    {
        $question = $this->find($id);
        if ($question) {
            $question->status = !$question->status;
            $question->save();
        }
        return $question;
    }

    public function getAllWithPagination(int $perPage): LengthAwarePaginator
    {
        return McqQuestion::with('question')->paginate($perPage);
    }

    public function findWithDetails(int $id)
    {
        return McqQuestion::with('question')->findOrFail($id);
    }
}
