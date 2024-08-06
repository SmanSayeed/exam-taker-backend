<?php

namespace App\Repositories\QuestionRepository;

use App\Models\CreativeQuestion;
use Illuminate\Pagination\LengthAwarePaginator;


class CreativeQuestionRepository implements QuestionRepositoryInterface
{
    public function create(array $data)
    {
        return CreativeQuestion::create($data);
    }

    public function update(int $id, array $data)
    {
        $creativeQuestion = CreativeQuestion::findOrFail($id);
        $creativeQuestion->update($data);
        return $creativeQuestion;
    }

    public function delete(int $id)
    {
        return CreativeQuestion::destroy($id);
    }

    public function find(int $id)
    {
        return CreativeQuestion::find($id);
    }

    public function getAll()
    {
        return CreativeQuestion::all();
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
        return CreativeQuestion::with('question')->paginate($perPage);
    }
    public function findWithDetails(int $id)
    {
        return CreativeQuestion::with('question')->findOrFail($id);
    }
}
