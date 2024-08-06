<?php

namespace App\DTOs\CreateQuestionDTO;

use Spatie\LaravelData\Data;

class NormalTextQuestionData extends Data
{
    public function __construct(
        public int $question_id,
        public string $normal_question_description
    ) {}
}
