<?php

namespace App\Services\StudentService;

use App\DTOs\StudentDTO\StudentRegistrationData;
use App\Http\Resources\StudentResource\StudentResource;
use App\Models\Student;
use App\Helpers\ApiResponseHelper;
use Exception;
use Illuminate\Support\Facades\Hash;

class StudentCRUDService
{

    public function store(StudentRegistrationData $data)
    {
        try {
            $studentData = $data->toArray();
            $studentData['password'] = Hash::make($studentData['password']);

            // Optional logic before creating student
            $student = Student::create($studentData);

            // Return success response
            return ApiResponseHelper::success('Student created successfully', new StudentResource($student));
        } catch (Exception $e) {
            // Return error response
            return ApiResponseHelper::error('Failed to create student: ' . $e->getMessage(), 500);
        }
    }

    public function update(Student $student, array $data)
    {
        try {
            $student->update($data);

            return ApiResponseHelper::success('Student updated successfully', new StudentResource($student));
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
