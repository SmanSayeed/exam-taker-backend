<?php

namespace App\DTOs\CreateQuestionDTO;

use Spatie\LaravelData\Data;

class QuestionData extends Data
{
    public function __construct(
        public string $title,
        public ?string $description,
        public ?array $images,
        public bool $is_paid,
        public bool $is_featured,
        public bool $status,
        public string $type,
        public int $mark,
        public string $created_by,
        public string $edited_by,
    ) {}
}
