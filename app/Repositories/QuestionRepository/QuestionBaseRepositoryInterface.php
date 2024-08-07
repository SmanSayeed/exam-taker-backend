<?php

namespace App\Repositories\QuestionRepository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
interface QuestionBaseRepositoryInterface
{
    public function setModel(Model $model): void;

    public function getAll(array $relations = [], int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Model;

    public function create(array $data): Model;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
