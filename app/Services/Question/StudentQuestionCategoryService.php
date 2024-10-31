<?php

namespace App\Services\Question;

use App\Repositories\QuestionRepository\QuestionCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StudentQuestionCategoryService
{
    protected ?Model $model = null;

    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    public function getAll(array $relations = [], int $perPage = 9999999999): LengthAwarePaginator
    {
        if (!$this->model) {
            throw new \Exception('Model not set.');
        }

        // Fetch the IDs of sections that have associated questions
        $sectionIdsWithQuestions = DB::table('questionables')
            ->whereNotNull('question_id')
            ->distinct()
            ->pluck('section_id'); // Adjust as needed for other category IDs

        // Query the sections table for those IDs
        return $this->model
            ->with($relations)
            ->whereIn('id', $sectionIdsWithQuestions) // Adjust this to the correct field in your main model
            ->paginate($perPage);
    }








    public function findById(int $id,array $relations = []): ?Model
    {
        if (!$this->model) {
            throw new \Exception('Model not set.');
        }
        $query= $this->model->find($id);
        return $query->with($relations)->first();
    }
}
