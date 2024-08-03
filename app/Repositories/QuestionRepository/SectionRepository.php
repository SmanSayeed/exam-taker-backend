<?php

namespace App\Repositories\QuestionRepository;

use App\Models\Section;

class SectionRepository extends BaseRepository implements SectionRepositoryInterface
{
    public function __construct(Section $model)
    {
        parent::__construct($model);
    }
}
