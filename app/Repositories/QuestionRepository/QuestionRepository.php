<?php

namespace App\Repositories\QuestionRepository;

use App\Models\Question;

class QuestionRepository implements QuestionRepositoryInterface
{
    public function create(array $data)
    {
        return Question::create($data);
    }

    public function update(int $id, array $data)
    {
        $question = Question::findOrFail($id);
        $question->update($data);
        return $question;
    }

    public function delete(int $id)
    {
        return Question::destroy($id);
    }

    public function find(int $id)
    {
        return Question::find($id);
    }

    public function getAll()
    {
        return Question::all();
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
