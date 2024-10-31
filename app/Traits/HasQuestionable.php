<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasQuestionable
{
    public function ifQuestionExists(): Builder
    {
        return $this->hasMany($this->getQuestionableModelClass())->whereHas('questionable', function ($query) {
            $query->whereNotNull('question_id');
        });
    }

    protected function getQuestionableModelClass(): string
    {
        // This should return the class name of the model that has the relationship.
        // Override this method in the model that uses the trait.
        throw new \Exception('Method getQuestionableModelClass() must be overridden in the model.');
    }
}
