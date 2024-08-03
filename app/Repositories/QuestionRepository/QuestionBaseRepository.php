<?php

namespace App\Repositories\QuestionRepository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class QuestionBaseRepository implements QuestionBaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $record = $this->findById($id);
        return $record ? $record->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $record = $this->findById($id);
        return $record ? $record->delete() : false;
    }
}
