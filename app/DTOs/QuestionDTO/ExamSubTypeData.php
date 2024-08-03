<?php
namespace App\DTOs\QuestionDTO;

use Spatie\LaravelData\Data;


class ExamSubTypeData extends QuestionEntityData
{
    public function __construct(
        public int $exam_type_id,
        string $title,
        ?string $details,
        ?string $image,
        bool $status
    ) {
        parent::__construct($title, $details, $image, $status);
    }
}
