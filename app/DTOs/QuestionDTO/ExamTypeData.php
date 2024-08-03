<?php
namespace App\DTOs\QuestionDTO;

use Spatie\LaravelData\Data;

class ExamTypeData extends Data
{
    public function __construct(
        public int $section_id,
        public string $title,
        public ?string $details,
        public ?string $image,
        public bool $status
    ) {}
}
