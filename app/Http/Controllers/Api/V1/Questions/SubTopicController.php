<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\DTOs\QuestionDTO\SubTopicData;
use App\Http\Requests\Api\V1\Questions\QuestionBaseRequest;
use App\Services\Question\QuestionBaseService;
use App\Models\SubTopic; // Import the model here
use Illuminate\Http\JsonResponse;

class SubTopicController extends QuestionBaseController
{
    public function __construct(QuestionBaseService $service)
    {
        parent::__construct($service);
        $this->service->setModel(new SubTopic()); // Pass the model to the service
    }

    protected function getDtoFromRequest(QuestionBaseRequest $request): SubTopicData
    {
        return SubTopicData::from($request->validated());
    }
}
