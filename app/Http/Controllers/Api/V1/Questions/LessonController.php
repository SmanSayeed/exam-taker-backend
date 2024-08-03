<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\DTOs\QuestionDTO\LessonData;
use App\Http\Requests\Api\V1\Questions\QuestionBaseRequest;
use App\Services\Question\QuestionBaseService;
use App\Models\Lesson; // Import the model here
use Illuminate\Http\JsonResponse;

class LessonController extends QuestionBaseController
{
    public function __construct(QuestionBaseService $service)
    {
        parent::__construct($service);
        $this->service->setModel(new Lesson()); // Pass the model to the service
    }

    protected function getDtoFromRequest(QuestionBaseRequest $request): LessonData
    {
        return LessonData::from($request->validated());
    }
}
