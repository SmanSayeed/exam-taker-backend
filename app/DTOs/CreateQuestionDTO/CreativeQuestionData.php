<?php

namespace App\DTOs\CreateQuestionDTO;

use Spatie\LaravelData\Data;

class CreativeQuestionData extends Data
{
    public function __construct(
        public int $question_id,
        public string $creative_question_text,
        public string $creative_question_type, //a,b,c,d
        public ?string $creative_question_text_description
    ) {}
}
