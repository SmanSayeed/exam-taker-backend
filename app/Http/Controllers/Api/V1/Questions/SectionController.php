<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\DTOs\QuestionDTO\SectionData;
use App\Http\Requests\Api\V1\Questions\QuestionBaseRequest;
use App\Services\Question\QuestionBaseService;
use App\Models\Section; // Import the model here
use Illuminate\Http\JsonResponse;

class SectionController extends QuestionBaseController
{
    public function __construct(QuestionBaseService $service)
    {
        parent::__construct($service);
        $this->service->setModel(new Section()); // Pass the model to the service
    }

    protected function getDtoFromRequest(QuestionBaseRequest $request): SectionData
    {
        return SectionData::from($request->validated());
    }
}
