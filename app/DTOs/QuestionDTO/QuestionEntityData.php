<?php
namespace App\DTOs\QuestionDTO;

use Spatie\LaravelData\Data;

class QuestionEntityData extends Data
{
    public function __construct(
        public string $title,
        public ?string $details,
        public ?string $image,
        public bool $status
    ) {}
}


