<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\DTOs\QuestionDTO\TopicData;
use App\Http\Requests\Api\V1\Questions\QuestionBaseRequest;
use App\Services\Question\QuestionBaseService;
use App\Models\Topic; // Import the model here
use Illuminate\Http\JsonResponse;

class TopicController extends QuestionBaseController
{
    public function __construct(QuestionBaseService $service)
    {
        parent::__construct($service);
        $this->service->setModel(new Topic()); // Pass the model to the service
    }

    protected function getDtoFromRequest(QuestionBaseRequest $request): TopicData
    {
        return TopicData::from($request->validated());
    }
}
