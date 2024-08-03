<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\DTOs\QuestionDTO\GroupData;
use App\Http\Requests\Api\V1\Questions\QuestionBaseRequest;
use App\Services\Question\QuestionBaseService;
use App\Models\Group; // Import the model here
use Illuminate\Http\JsonResponse;

class GroupController extends QuestionBaseController
{
    public function __construct(QuestionBaseService $service)
    {
        parent::__construct($service);
        $this->service->setModel(new Group()); // Pass the model to the service
    }

    protected function getDtoFromRequest(QuestionBaseRequest $request): GroupData
    {
        return GroupData::from($request->validated());
    }
}
