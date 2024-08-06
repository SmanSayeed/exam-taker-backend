<?php
namespace App\Rules;

use App\Models\Question;
use Illuminate\Contracts\Validation\Rule;

class ValidateQuestionType implements Rule
{
    private $type;
    private $questionId;

    public function __construct($type, $questionId = null)
    {
        $this->type = $type;
        $this->questionId = $questionId;
    }

    public function passes($attribute, $value)
    {
        $query = Question::where('id', $value);

        if ($this->questionId) {
            $query->where('id', '!=', $this->questionId);
        }

        $question = $query->first();

        if ($question && $question->type !== $this->type) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return 'The question type does not match the existing question type.';
    }
}
