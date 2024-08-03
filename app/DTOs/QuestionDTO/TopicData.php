<?php
namespace App\DTOs\QuestionDTO;

use Spatie\LaravelData\Data;




class TopicData extends QuestionEntityData
{
    public function __construct(
        public int $lesson_id,
        string $title,
        ?string $description,
        ?string $image,
        bool $status
    ) {
        parent::__construct($title, $description, $image, $status);
    }
}
