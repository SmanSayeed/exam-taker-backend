<?php

namespace App\Repositories\Admin\StudentCRUDRepository;

use App\Models\Student;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
interface StudentCRUDRepositoryInterface
{
    public function create(array $data): Student;
    public function findByEmail(string $email): ?Student;
    public function update(Student $student, array $data): bool;
    public function delete(Student $student): bool;
    public function getAll(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Student;
}
