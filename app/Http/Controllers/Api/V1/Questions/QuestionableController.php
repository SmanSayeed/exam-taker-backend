<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\DTOs\CreateQuestionDTO\AttachTypeData;
use App\Http\Controllers\Controller;
use App\Helpers\ApiResponseHelper;
use App\Http\Requests\AttachTypeRequest;
use App\Http\Requests\DetachTypeRequest; // Ensure this request is used if you have a specific class for detaching
use App\Models\Questionable;
use Illuminate\Http\JsonResponse;

class QuestionableController extends Controller
{
    /**
     * Attach types to a question.
     *
     * @param \App\Http\Requests\AttachTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function attach(AttachTypeRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        // Convert the validated data into a DTO
        $dto = new AttachTypeData(
            $validatedData['question_id'],
            $validatedData['section_id'] ?? null,
            $validatedData['exam_type_id'] ?? null,
            $validatedData['exam_sub_type_id'] ?? null,
            $validatedData['group_id'] ?? null,
            $validatedData['level_id'] ?? null,
            $validatedData['subject_id'] ?? null,
            $validatedData['lesson_id'] ?? null,
            $validatedData['topic_id'] ?? null,
            $validatedData['sub_topic_id'] ?? null
        );

        try {
            // Ensure there is a record for the question
            $questionable = Questionable::firstOrCreate(['question_id' => $dto->question_id]);

            // Detach existing types
            $this->detachTypes($dto->question_id, array_keys(array_filter([
                'section_id' => $dto->section_id,
                'exam_type_id' => $dto->exam_type_id,
                'exam_sub_type_id' => $dto->exam_sub_type_id,
                'group_id' => $dto->group_id,
                'level_id' => $dto->level_id,
                'subject_id' => $dto->subject_id,
                'lesson_id' => $dto->lesson_id,
                'topic_id' => $dto->topic_id,
                'sub_topic_id' => $dto->sub_topic_id
            ])));

            // Attach new types
            foreach ([
                'section_id' => $dto->section_id,
                'exam_type_id' => $dto->exam_type_id,
                'exam_sub_type_id' => $dto->exam_sub_type_id,
                'group_id' => $dto->group_id,
                'level_id' => $dto->level_id,
                'subject_id' => $dto->subject_id,
                'lesson_id' => $dto->lesson_id,
                'topic_id' => $dto->topic_id,
                'sub_topic_id' => $dto->sub_topic_id
            ] as $key => $typeId) {
                if ($typeId) {
                    $questionable->{$key} = $typeId;
                }
            }

            $questionable->save();

            return ApiResponseHelper::success($questionable, 'Types attached successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to attach types', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Detach types from a question.
     *
     * @param \App\Http\Requests\DetachTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detach(DetachTypeRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        // Convert the validated data into a DTO
        $dto = new AttachTypeData(
            $validatedData['question_id'],
            $validatedData['section_id'] ?? null,
            $validatedData['exam_type_id'] ?? null,
            $validatedData['exam_sub_type_id'] ?? null,
            $validatedData['group_id'] ?? null,
            $validatedData['level_id'] ?? null,
            $validatedData['subject_id'] ?? null,
            $validatedData['lesson_id'] ?? null,
            $validatedData['topic_id'] ?? null,
            $validatedData['sub_topic_id'] ?? null
        );

        try {
            $questionable = Questionable::where('question_id', $dto->question_id)->first();

            if (!$questionable) {
                return ApiResponseHelper::error('No record found for the question', 404);
            }

            // Detach specified types
            foreach ([
                'section_id' => $dto->section_id,
                'exam_type_id' => $dto->exam_type_id,
                'exam_sub_type_id' => $dto->exam_sub_type_id,
                'group_id' => $dto->group_id,
                'level_id' => $dto->level_id,
                'subject_id' => $dto->subject_id,
                'lesson_id' => $dto->lesson_id,
                'topic_id' => $dto->topic_id,
                'sub_topic_id' => $dto->sub_topic_id
            ] as $key => $typeId) {
                if ($typeId) {
                    if ($this->canDetachType($key, $questionable)) {
                        $questionable->{$key} = null;
                    } else {
                        return ApiResponseHelper::error('Cannot detach type as it has child types', 400);
                    }
                }
            }

            $questionable->save();

            return ApiResponseHelper::success($questionable, 'Types detached successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to detach types', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Determine if a type can be detached.
     *
     * @param string $typeKey
     * @param \App\Models\Questionable $questionable
     * @return bool
     */
    protected function canDetachType(string $typeKey, Questionable $questionable): bool
    {
        $childKeys = $this->getChildKeys($typeKey);

        foreach ($childKeys as $childKey) {
            if ($questionable->{$childKey}) {
                return false; // If any child type is present, cannot detach parent
            }
        }

        return true;
    }

    /**
     * Get child keys for a given parent key.
     *
     * @param string $typeKey
     * @return array
     */
    protected function getChildKeys(string $typeKey): array
    {
        $hierarchy = [
            'section_id' => ['exam_type_id'],
            'exam_type_id' => ['exam_sub_type_id'],
            'group_id' => ['level_id'],
            'level_id' => ['subject_id'],
            'subject_id' => ['lesson_id'],
            'lesson_id' => ['topic_id'],
            'topic_id' => ['sub_topic_id'],
        ];

        return $hierarchy[$typeKey] ?? [];
    }

    /**
     * Detach existing types.
     *
     * @param int $questionId
     * @param array $typeKeys
     * @return void
     */
    protected function detachTypes(int $questionId, array $typeKeys)
    {
        $questionable = Questionable::where('question_id', $questionId)->first();

        if ($questionable) {
            foreach ($typeKeys as $typeKey) {
                $questionable->{$typeKey} = null;
            }
            $questionable->save();
        }
    }
}
