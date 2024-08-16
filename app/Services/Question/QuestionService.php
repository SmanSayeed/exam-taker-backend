<?php
namespace App\Services\Question;

use App\DTOs\CreateQuestionDTO\QuestionData;
use App\DTOs\CreateQuestionDTO\McqQuestionData;
use App\DTOs\CreateQuestionDTO\NormalTextQuestionData;
use App\DTOs\CreateQuestionDTO\CreativeQuestionData;
use App\Models\Question;
use App\Repositories\QuestionRepository\QuestionRepositoryInterface;
use App\Repositories\QuestionRepository\CreativeQuestionRepository;
use App\Repositories\QuestionRepository\McqQuestionRepository;
use App\Repositories\QuestionRepository\NormalTextQuestionRepository;
use App\Repositories\QuestionRepository\QuestionRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
class QuestionService
{
    protected $questionRepository;
    protected $mcqQuestionRepository;
    protected $normalTextQuestionRepository;
    protected $creativeQuestionRepository;
    protected Model $model;

    public function __construct(
        QuestionRepository $questionRepository,
        McqQuestionRepository $mcqQuestionRepository,
        NormalTextQuestionRepository $normalTextQuestionRepository,
        CreativeQuestionRepository $creativeQuestionRepository
    ) {
        $this->questionRepository = $questionRepository;
        $this->mcqQuestionRepository = $mcqQuestionRepository;
        $this->normalTextQuestionRepository = $normalTextQuestionRepository;
        $this->creativeQuestionRepository = $creativeQuestionRepository;
    }



    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    public function createQuestion(QuestionData $questionDTO)
    {
        return $this->questionRepository->create($questionDTO->toArray());
    }

    public function createMcqQuestion(McqQuestionData $mcqQuestionDTO)
    {
        return $this->mcqQuestionRepository->create($mcqQuestionDTO->toArray());
    }

    public function createNormalTextQuestion(NormalTextQuestionData $normalTextQuestionDTO)
    {
        return $this->normalTextQuestionRepository->create($normalTextQuestionDTO->toArray());
    }

    public function createCreativeQuestion(CreativeQuestionData $creativeQuestionDTO)
    {
        return $this->creativeQuestionRepository->create($creativeQuestionDTO->toArray());
    }

    public function updateQuestion(int $id, QuestionData $questionDTO)
    {
        return $this->questionRepository->update($id, $questionDTO->toArray());
    }

    public function updateMcqQuestion(int $id, McqQuestionData $mcqQuestionDTO)
    {
        return $this->mcqQuestionRepository->update($id, $mcqQuestionDTO->toArray());
    }

    public function updateNormalTextQuestion(int $id, NormalTextQuestionData $normalTextQuestionDTO)
    {
        return $this->normalTextQuestionRepository->update($id, $normalTextQuestionDTO->toArray());
    }

    public function updateCreativeQuestion(int $id, CreativeQuestionData $creativeQuestionDTO)
    {
        return $this->creativeQuestionRepository->update($id, $creativeQuestionDTO->toArray());
    }


    public function getQuestion(int $id)
    {
        return $this->questionRepository->find($id);
    }

    public function getAllQuestions()
    {
        return $this->questionRepository->getAll();
    }

    public function changeStatus(int $id)
    {
        return $this->questionRepository->changeStatus($id);
    }

    public function deleteQuestion(int $id)
    {
        return $this->questionRepository->delete($id);
    }

    public function deleteMcqQuestion(int $id)
    {
        return $this->mcqQuestionRepository->delete($id);
    }

    public function deleteNormalTextQuestion(int $id)
    {
        return $this->normalTextQuestionRepository->delete($id);
    }

    public function deleteCreativeQuestion(int $id)
    {
        return $this->creativeQuestionRepository->delete($id);
    }

    public function getAllMcqQuestions(int $perPage)
    {
        return $this->mcqQuestionRepository->getAllWithPagination($perPage);
    }

    public function getAllCreativeQuestions(int $perPage)
    {
        return $this->creativeQuestionRepository->getAllWithPagination($perPage);
    }

    public function getMcqQuestion(int $id)
    {
        return $this->mcqQuestionRepository->findWithDetails($id);
    }

    public function getCreativeQuestion(int $id)
    {
        return $this->creativeQuestionRepository->findWithDetails($id);
    }

    public function getTypesWithQuestions(string $type = null, int $perPage = 10)
    {
        switch ($type) {
            case 'mcq':
                return $this->mcqQuestionRepository->getAllWithPagination($perPage);
            case 'creative':
                return $this->creativeQuestionRepository->getAllWithPagination($perPage);
            case 'normal':
                return $this->questionRepository->getNormalQuestionWithPagination($perPage);
            default:
                return $this->questionRepository->getAllWithPagination($perPage); // Fallback to generic
        }
    }

    public function getQuestionsByType(?string $type, int $perPage)
    {
        return $this->questionRepository->getQuestionsWithTypes($type, $perPage);
    }

    public function searchAndFilterQuestions(array $filters, string $keyword = '', int $perPage = 10)
{
    // Start with the base query for questions
    $query = Question::query();

    // Apply category filters if provided
    if (
        isset($filters['section_id']) ||
        isset($filters['exam_type_id']) ||
        isset($filters['exam_sub_type_id']) ||
        isset($filters['group_id']) ||
        isset($filters['level_id']) ||
        isset($filters['subject_id']) ||
        isset($filters['lesson_id']) ||
        isset($filters['topic_id']) ||
        isset($filters['sub_topic_id'])
    ) {
        $query->whereHas('attachable', function ($q) use ($filters) {
            if (isset($filters['section_id'])) {
                $q->where('section_id', $filters['section_id']);
            }
            if (isset($filters['exam_type_id'])) {
                $q->where('exam_type_id', $filters['exam_type_id']);
            }
            if (isset($filters['exam_sub_type_id'])) {
                $q->where('exam_sub_type_id', $filters['exam_sub_type_id']);
            }
            if (isset($filters['group_id'])) {
                $q->where('group_id', $filters['group_id']);
            }
            if (isset($filters['level_id'])) {
                $q->where('level_id', $filters['level_id']);
            }
            if (isset($filters['subject_id'])) {
                $q->where('subject_id', $filters['subject_id']);
            }
            if (isset($filters['lesson_id'])) {
                $q->where('lesson_id', $filters['lesson_id']);
            }
            if (isset($filters['topic_id'])) {
                $q->where('topic_id', $filters['topic_id']);
            }
            if (isset($filters['sub_topic_id'])) {
                $q->where('sub_topic_id', $filters['sub_topic_id']);
            }
        });
    }

    // Apply keyword search if provided
    if ($keyword) {
        $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', '%' . $keyword . '%')
              ->orWhere('description', 'like', '%' . $keyword . '%');
        });
    }

    // Apply type-specific relationships if provided
    $type = $filters['type'] ?? null;
    switch ($type) {
        case 'mcq':
            $query->with('mcqQuestions');
            break;
        case 'creative':
            $query->with('creativeQuestions');
            break;
        case 'normal':
            // No additional relationships to include
            break;
        default:
            $query->with(['creativeQuestions', 'mcqQuestions']);
            break;
    }

    // Load the attachable relationship and paginate the results
    $query->with('attachable')->orderBy('created_at', 'desc');
    $results = $query->paginate($perPage);

    return $results;
}


    // public function searchAndFilterQuestions(array $filters, string $keyword, int $perPage = 10)
    // {
    //     // Start with the base query for questions
    //     $query = Question::query();

    //     // Apply category filters if provided
    //     if (isset($filters['section_id']) || isset($filters['exam_type_id']) || isset($filters['exam_sub_type_id'])
    //         || isset($filters['group_id']) || isset($filters['level_id']) || isset($filters['subject_id'])
    //         || isset($filters['lesson_id']) || isset($filters['topic_id']) || isset($filters['sub_topic_id'])) {

    //         $query->whereHas('attachable', function ($q) use ($filters) {
    //             if (isset($filters['section_id'])) {
    //                 $q->where('section_id', $filters['section_id']);
    //             }
    //             if (isset($filters['exam_type_id'])) {
    //                 $q->where('exam_type_id', $filters['exam_type_id']);
    //             }
    //             if (isset($filters['exam_sub_type_id'])) {
    //                 $q->where('exam_sub_type_id', $filters['exam_sub_type_id']);
    //             }
    //             if (isset($filters['group_id'])) {
    //                 $q->where('group_id', $filters['group_id']);
    //             }
    //             if (isset($filters['level_id'])) {
    //                 $q->where('level_id', $filters['level_id']);
    //             }
    //             if (isset($filters['subject_id'])) {
    //                 $q->where('subject_id', $filters['subject_id']);
    //             }
    //             if (isset($filters['lesson_id'])) {
    //                 $q->where('lesson_id', $filters['lesson_id']);
    //             }
    //             if (isset($filters['topic_id'])) {
    //                 $q->where('topic_id', $filters['topic_id']);
    //             }
    //             if (isset($filters['sub_topic_id'])) {
    //                 $q->where('sub_topic_id', $filters['sub_topic_id']);
    //             }
    //         });
    //     }

    //     // Apply keyword search if provided
    //     if ($keyword) {
    //         $query->where(function ($q) use ($keyword) {
    //             $q->where('title', 'like', '%' . $keyword . '%')
    //               ->orWhere('description', 'like', '%' . $keyword . '%');
    //         });
    //     }

    //     // Apply type-specific relationships
    //     switch ($filters['type'] ?? 'all') {
    //         case "mcq":
    //             $query->with(['mcqQuestions']);
    //             break;
    //         case "creative":
    //             $query->with(['creativeQuestions']);
    //             break;
    //         case "normal":
    //             // No additional relationships to include
    //             break;
    //         default:
    //             $query->with(['creativeQuestions', 'mcqQuestions']);
    //             break;
    //     }

    //     // Load the attachable relationship and paginate the results
    //     $query->with('attachable')->orderBy('created_at', 'desc');
    //     $results = $query->paginate($perPage);

    //     return $results;
    // }






}
