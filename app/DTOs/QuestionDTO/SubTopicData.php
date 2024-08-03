<?php
namespace App\DTOs\QuestionDTO;

use Spatie\LaravelData\Data;


class SubTopicData extends QuestionEntityData
{
    public function __construct(
        public int $topic_id,
        string $title,
        ?string $details,
        bool $status,
        ?string $image
    ) {
        parent::__construct($title, $details, $image, $status);
    }
}

