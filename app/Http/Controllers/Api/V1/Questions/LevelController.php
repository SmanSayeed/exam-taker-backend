<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\DTOs\QuestionDTO\LevelData;
use App\Http\Requests\Api\V1\Questions\QuestionBaseRequest;
use App\Services\Question\QuestionBaseService;
use App\Models\Level; // Import the model here
use Illuminate\Http\JsonResponse;

class LevelController extends QuestionBaseController
{
    public function __construct(QuestionBaseService $service)
    {
        parent::__construct($service);
        $this->service->setModel(new Level()); // Pass the model to the service
    }

    protected function getDtoFromRequest(QuestionBaseRequest $request): LevelData
    {
        return LevelData::from($request->validated());
    }
}
