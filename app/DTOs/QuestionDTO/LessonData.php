<?php
namespace App\DTOs\QuestionDTO;

use Spatie\LaravelData\Data;


class LessonData extends QuestionEntityData
{
    public function __construct(
        public int $subject_id,
        string $title,
        ?string $details,
        bool $status,
        ?string $image
    ) {
        parent::__construct($title, $details, $image, $status);
    }
}
