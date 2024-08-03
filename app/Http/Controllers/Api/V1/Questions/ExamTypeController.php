<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\DTOs\QuestionDTO\ExamTypeData;
use App\Http\Requests\Api\V1\Questions\QuestionBaseRequest;
use App\Services\Question\QuestionBaseService;
use App\Models\ExamType; // Import the model here
use Illuminate\Http\JsonResponse;

class ExamTypeController extends QuestionBaseController
{
    public function __construct(QuestionBaseService $service)
    {
        parent::__construct($service);
        $this->service->setModel(new ExamType()); // Pass the model to the service
    }

    protected function getDtoFromRequest(QuestionBaseRequest $request): ExamTypeData
    {
        return ExamTypeData::from($request->validated());
    }
}
