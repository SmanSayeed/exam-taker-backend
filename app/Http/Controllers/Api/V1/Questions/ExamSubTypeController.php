<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\DTOs\QuestionDTO\ExamSubTypeData;
use App\Http\Requests\Api\V1\Questions\QuestionBaseRequest;
use App\Services\Question\QuestionBaseService;
use App\Models\ExamSubType; // Import the model here
use Illuminate\Http\JsonResponse;

class ExamSubTypeController extends QuestionBaseController
{
    public function __construct(QuestionBaseService $service)
    {
        parent::__construct($service);
        $this->service->setModel(new ExamSubType()); // Pass the model to the service
    }

    protected function getDtoFromRequest(QuestionBaseRequest $request): ExamSubTypeData
    {
        return ExamSubTypeData::from($request->validated());
    }
}
