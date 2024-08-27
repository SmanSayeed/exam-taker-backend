<?php

namespace App\Repositories\QuestionRepository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class QueBaseRepository implements QuestionRepositoryInterface
{
    protected Model $model;

    /**
     * Set the model instance.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }

    public function find(int $id)
    {
        $query = $this->model->with('attachable')->with(['creativeQuestions','mcqQuestions'])->where('id',$id)->first();
        return $query;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function changeStatus(int $id)
    {
        $record = $this->find($id);
        if ($record) {
            $record->status = !$record->status;
            $record->save();
        }
        return $record;
    }

    public function getAllWithPagination(int $perPage): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }
}
