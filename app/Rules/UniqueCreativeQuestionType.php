<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\CreativeQuestion;

class UniqueCreativeQuestionType implements Rule
{
    protected $questionId;
    protected $type;

    public function __construct($questionId, $type)
    {
        $this->questionId = $questionId;
        $this->type = $type;
    }

    public function passes($attribute, $value)
    {
        return !CreativeQuestion::where('question_id', $this->questionId)
                                ->where('creative_question_type', $this->type)
                                ->exists();
    }

    public function message()
    {
        return 'The creative question type has already been created for this question.';
    }
}
