<?php

namespace App\Repositories\QuestionRepository;

use App\Models\NormalTextQuestion;

class NormalTextQuestionRepository implements QuestionRepositoryInterface
{
    public function create(array $data)
    {
        return NormalTextQuestion::create($data);
    }

    public function update(int $id, array $data)
    {
        $normalTextQuestion = NormalTextQuestion::findOrFail($id);
        $normalTextQuestion->update($data);
        return $normalTextQuestion;
    }

    public function delete(int $id)
    {
        return NormalTextQuestion::destroy($id);
    }

    public function find(int $id)
    {
        return NormalTextQuestion::find($id);
    }

    public function getAll()
    {
        return NormalTextQuestion::all();
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
}
