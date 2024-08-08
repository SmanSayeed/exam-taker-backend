<?php
namespace App\DTOs\CreateQuestionDTO;

use Spatie\LaravelData\Data;

class AttachTypeData extends Data
{
    public function __construct(
        public int $question_id,
        public ?int $section_id,
        public ?int $exam_type_id,
        public ?int $exam_sub_type_id,
        public ?int $group_id,
        public ?int $level_id,
        public ?int $subject_id,
        public ?int $lesson_id,
        public ?int $topic_id,
        public ?int $sub_topic_id
    ) {}
}
