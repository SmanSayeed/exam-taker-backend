<?php
namespace App\DTOs\QuestionDTO;

use Spatie\LaravelData\Data;




class SubjectData extends QuestionEntityData
{
    public function __construct(
        public int $level_id,
        public int $group_id,
        public string $part,
        string $title,
        ?string $details,
        ?string $image,
        bool $status
    ) {
        parent::__construct($title, $details, $image, $status);
    }
}
