<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class ModelTestWithQuestionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        if ($request->has('question_id')) {
            $questionId = $request->input('question_id');
            $question = $this->questions->firstWhere('id', $questionId);
            $data['question'] = $question ? new QuestionResource($question) : null;
        }
        return $data;
    }
}
