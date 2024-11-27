<?php

namespace App\Services\Question;

use App\Repositories\QuestionRepository\QuestionCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionCategoryService
{
    protected QuestionCategoryRepositoryInterface $repository;

    public function __construct(QuestionCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function setModel(Model $model): void
    {
        $this->repository->setModel($model);
    }

    public function getAll(array $relations = [], int $perPage = 9): LengthAwarePaginator
    {
        return $this->repository->getAll($relations, $perPage);
    }

    public function findById(int $id, array $relations = []): ?Model
    {
        $query = $this->repository->findById($id);
        return $query->with($relations)->first();
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
