<?php

namespace App\Services\Question;

use App\Repositories\QuestionRepository\QuestionBaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionBaseService
{
    protected QuestionBaseRepositoryInterface $repository;

    public function __construct(QuestionBaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function setModel(Model $model): void
    {
        $this->repository->setModel($model);
    }

    public function getAll(array $relations = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($relations,$perPage);
    }

    public function findById(int $id): ?Model
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
