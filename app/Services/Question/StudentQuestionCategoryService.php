<?php

namespace App\Services\Question;

use App\Repositories\QuestionRepository\QuestionCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentQuestionCategoryService
{
    protected ?Model $model = null;

    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    public function getAll(string $categoryType, array $relations = [], int $perPage = 15): LengthAwarePaginator
    {
        if (!$this->model) {
            throw new \Exception('Model not set.');
        }

        // Validate the categoryType column exists in the questionables table
        if (!Schema::hasColumn('questionables', $categoryType)) {
            throw new \InvalidArgumentException("Invalid column: {$categoryType}");
        }

        // Get category IDs where at least one question_id exists
        $categoryIdsWithQuestions = DB::table('questionables')
            ->whereNotNull('question_id') // Ensure question_id is not null
            ->whereNotNull($categoryType) // Ensure categoryType is not null
            ->groupBy($categoryType) // Group by the dynamic categoryType
            ->havingRaw('COUNT(*) > 0') // Only include groups with at least one record
            ->pluck($categoryType);

        // If no category IDs match, return an empty paginated result
        if ($categoryIdsWithQuestions->isEmpty()) {
            return $this->model->newQuery()->paginate($perPage); // Return empty paginated result
        }

        // Fetch models that match the filtered category IDs
        return $this->model
            ->with($relations)
            ->whereIn('id', $categoryIdsWithQuestions)
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
