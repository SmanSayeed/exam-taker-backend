<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponseHelper;
use App\Http\Requests\AttachTypeRequest;
use App\Models\Questionable;
use Illuminate\Http\Request;

class QuestionableController extends Controller
{
    /**
     * Attach types to a question.
     *
     * @param \App\Http\Requests\AttachTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function attach(AttachTypeRequest $request)
    {
        $validatedData = $request->validated();
        $questionId = $validatedData['question_id'];
        $types = $validatedData['types'];

        try {
            // Ensure there is a record for the question
            $questionable = Questionable::firstOrCreate(['question_id' => $questionId]);

            // Detach existing types
            $this->detachTypes($questionId, array_keys($types));

            // Attach new types
            foreach ($types as $key => $typeId) {
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
     * @param \App\Http\Requests\AttachTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detach(AttachTypeRequest $request)
    {
        $validatedData = $request->validated();
        $questionId = $validatedData['question_id'];
        $types = $validatedData['types'];

        try {
            $questionable = Questionable::where('question_id', $questionId)->first();

            if (!$questionable) {
                return ApiResponseHelper::error('No record found for the question', 404);
            }

            // Detach specified types
            foreach ($types as $key => $typeId) {
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
}
