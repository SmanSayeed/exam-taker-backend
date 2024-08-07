<?php

namespace App\DTOs\CreateQuestionDTO;

use Spatie\LaravelData\Data;

class AttachTypeData extends Data
{
    public function __construct(
        public int $question_id,
        public array $types // This will contain the types to attach or detach
    ) {}
}
