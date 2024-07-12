<?php

namespace App\Http\Controllers\Api\V1\Admin\ManageStudents;

use App\DTOs\StudentDTO\StudentRegistrationData;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest\StudentRegistrationRequest;
use App\Http\Requests\StudentRequest\StudentUpdateRequest;
use App\Http\Resources\StudentResource\StudentResource;
use App\Models\Student;
use App\Services\StudentService\StudentCRUDService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class StudentCRUDController extends Controller
{
    protected $studentService;

    public function __construct(StudentCRUDService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index():JsonResponse
    {
        try {
            $students = Student::all();
            return ApiResponseHelper::success(StudentResource::collection($students), 'Students retrieved successfully');
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve students: ' . $e->getMessage());
        }
    }

    public function store(StudentRegistrationRequest $request):JsonResponse
    {
        try {
            $studentData = StudentRegistrationData::from($request->validated());

            $result = $this->studentService->store($studentData);

            return $result;
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to register student: ' . $e->getMessage()], 500);
        }
    }

    public function show(Student $student)
    {
        return new StudentResource($student);
    }

    public function update(StudentUpdateRequest $request, Student $student)
    {
        $student->update($request->validated());
        return new StudentResource($student);
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully']);
    }
}
