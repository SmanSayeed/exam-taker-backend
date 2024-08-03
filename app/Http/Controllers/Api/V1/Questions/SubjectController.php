<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\DTOs\QuestionDTO\SubjectData;
use App\Http\Requests\Api\V1\Questions\QuestionBaseRequest;
use App\Services\Question\QuestionBaseService;
use App\Models\Subject; // Import the model here
use Illuminate\Http\JsonResponse;

class SubjectController extends QuestionBaseController
{
    public function __construct(QuestionBaseService $service)
    {
        parent::__construct($service);
        $this->service->setModel(new Subject()); // Pass the model to the service
    }

    protected function getDtoFromRequest(QuestionBaseRequest $request): SubjectData
    {
        return SubjectData::from($request->validated());
    }
}
