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
class SectionData extends QuestionEntityData
{
    public function __construct(
        string $title,
        ?string $details,
        ?string $image,
        bool $status
    ) {
        parent::__construct($title, $details, $image, $status);
    }
}

class ExamTypeData extends QuestionEntityData
{
    public function __construct(
        public int $section_id,
        string $title,
        ?string $details,
        ?string $image,
        bool $status
    ) {
        parent::__construct($title, $details, $image, $status);
    }
}

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

class YearData extends QuestionEntityData
{
    public function __construct(
        public int $section_id,
        string $title,
        ?string $details,
        ?string $image,
        bool $status
    ) {
        parent::__construct($title, $details, $image, $status);
    }
}

class GroupData extends QuestionEntityData
{
    public function __construct(
        string $title,
        ?string $details,
        ?string $image,
        bool $status
    ) {
        parent::__construct($title, $details, $image, $status);
    }
}

class LevelData extends QuestionEntityData
{
    public function __construct(
        string $title,
        ?string $details,
        ?string $image,
        bool $status
    ) {
        parent::__construct($title, $details, $image, $status);
    }
}

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
