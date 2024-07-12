<?php

namespace App\Repositories\Admin\StudentCRUDRepository;

use App\Models\Student;
use App\Repositories\Admin\StudentCRUDRepository\StudentCRUDRepositoryInterface;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class StudentCRUDRepository implements StudentCRUDRepositoryInterface
{
    public function create(array $data): Student
    {
        try {
            $student = new Student($data);
            $student->save();
            return $student;
        } catch (Exception $e) {
            throw new Exception('Error creating student: ' . $e->getMessage());
        }
    }

    public function findByEmail(string $email): ?Student
    {
        return Student::where('email', $email)->first();
    }

    public function update(Student $student, array $data): bool
    {
        try {
            return $student->update($data);
        } catch (Exception $e) {
            throw new Exception('Error updating student: ' . $e->getMessage());
        }
    }

    public function delete(Student $student): bool
    {
        try {
            return $student->delete();
        } catch (Exception $e) {
            throw new Exception('Error deleting student: ' . $e->getMessage());
        }
    }

    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Student::orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Student
    {
        return Student::find($id);
    }
}
