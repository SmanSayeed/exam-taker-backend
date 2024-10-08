<?php
namespace App\DTOs\QuestionDTO;

use Spatie\LaravelData\Data;

class SectionData extends QuestionEntityData
{
    public function __construct(
        ?string $title,
        ?string $details = null,
        ?string $image = null,
        bool $status
    ) {
        parent::__construct($title, $details, $image, $status);
    }
}
