<?php

namespace App\DTOs\CreateQuestionDTO;

use Spatie\LaravelData\Data;

class McqQuestionData extends Data
{
    public function __construct(
        public int $question_id,
        public string $mcq_question_text,
        public string $mcq_option_serial,
        public bool $is_correct,
        public ?string $description
    ) {}
}
