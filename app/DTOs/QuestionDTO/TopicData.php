<?php
namespace App\DTOs\QuestionDTO;

use Spatie\LaravelData\Data;




class TopicData extends QuestionEntityData
{
    public function __construct(
        public int $lesson_id,
        string $title,
        ?string $details,
        ?string $image,
        bool $status
    ) {
        parent::__construct($title, $details, $image, $status);
    }
}
