<?php

namespace App\Repositories\QuestionRepository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface QuestionBaseRepositoryInterface
{
    public function setModel(Model $model): void;

    public function getAll(): Collection;

    public function findById(int $id): ?Model;

    public function create(array $data): Model;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
