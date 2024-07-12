<?php

namespace App\Services\StudentService;

use App\DTOs\StudentDTO\StudentRegistrationData;
use App\Http\Resources\StudentResource\StudentResource;
use App\Models\Student;
use App\Helpers\ApiResponseHelper;
use App\Repositories\Admin\StudentCRUDRepository\StudentCRUDRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;

class StudentCRUDService
{
    protected StudentCRUDRepositoryInterface $studentRepository;

    public function __construct(StudentCRUDRepositoryInterface $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function getAll(int $perPage = 15)
    {
        return $this->studentRepository->getAll($perPage);
    }

    public function store(array $studentData)
    {
        try {
            $studentData['password'] = Hash::make($studentData['password']);
            unset($studentData['password_confirmation']); // Remove password confirmation field
            // Optional logic before creating student
            $student = $this->studentRepository->create($studentData);
            // Return success response
            return $student;
        } catch (Exception $e) {
            // Return error response
            return ApiResponseHelper::error('Failed to create student: ' . $e->getMessage(), 500);
        }
    }

    public function update(Student $student, array $data)
    {
        try {
            $student->update($data);
            return $student;
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to update student: ' . $e->getMessage(), 500);
        }
    }

    public function delete(Student $student)
    {
        try {
            $student->delete();

            return ApiResponseHelper::success('Student deleted successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to delete student: ' . $e->getMessage(), 500);
        }
    }
}
