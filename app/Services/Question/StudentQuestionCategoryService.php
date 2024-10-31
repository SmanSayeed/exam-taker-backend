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

    public function getAll(string $categoryType, array $relations = [], int $perPage = 9999999999): LengthAwarePaginator
    {
        if (!$this->model) {
            throw new \Exception('Model not set.');
        }

        // Fetch the IDs of categories that have associated questions dynamically
        $categoryIdsWithQuestions = DB::table('questionables')
            ->whereNotNull('question_id')
            ->distinct()
            ->pluck($categoryType); // Use the dynamic category type

        // Query the sections table for those IDs
        return $this->model
            ->with($relations)
            ->whereIn('id', $categoryIdsWithQuestions) // Adjust this to the correct field in your main model
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
