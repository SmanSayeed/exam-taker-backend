<?php

namespace App\Repositories\QuestionRepository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class QuestionBaseRepository implements QuestionBaseRepositoryInterface
{
    protected ?Model $model = null;

    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        if (!$this->model) {
            throw new \Exception('Model not set.');
        }
        return $this->model->all();
    }

    public function findById(int $id): ?Model
    {
        if (!$this->model) {
            throw new \Exception('Model not set.');
        }
        return $this->model->find($id);
    }

    public function create(array $data): Model
    {
        if (!$this->model) {
            throw new \Exception('Model not set.');
        }
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        if (!$this->model) {
            throw new \Exception('Model not set.');
        }
        $record = $this->findById($id);
        return $record ? $record->update($data) : false;
    }

    public function delete(int $id): bool
    {
        if (!$this->model) {
            throw new \Exception('Model not set.');
        }
        $record = $this->findById($id);
        return $record ? $record->delete() : false;
    }
}
