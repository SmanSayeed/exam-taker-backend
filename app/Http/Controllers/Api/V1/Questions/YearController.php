<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\DTOs\QuestionDTO\YearData;
use App\Http\Requests\Api\V1\Questions\QuestionBaseRequest;
use App\Services\Question\QuestionBaseService;
use App\Models\Year; // Import the model here
use Illuminate\Http\JsonResponse;

class YearController extends QuestionBaseController
{
    public function __construct(QuestionBaseService $service)
    {
        parent::__construct($service);
        $this->service->setModel(new Year()); // Pass the model to the service
    }

    protected function getDtoFromRequest(QuestionBaseRequest $request): YearData
    {
        return YearData::from($request->validated());
    }
}
