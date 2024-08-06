<?php
namespace App\Services\Question;

use App\DTOs\CreateQuestionDTO\QuestionData;
use App\DTOs\CreateQuestionDTO\McqQuestionData;
use App\DTOs\CreateQuestionDTO\NormalTextQuestionData;
use App\DTOs\CreateQuestionDTO\CreativeQuestionData;
use App\Repositories\QuestionRepository\QuestionRepositoryInterface;
use App\Repositories\QuestionRepository\CreativeQuestionRepository;
use App\Repositories\QuestionRepository\McqQuestionRepository;
use App\Repositories\QuestionRepository\NormalTextQuestionRepository;
use App\Repositories\QuestionRepository\QuestionRepository;

class QuestionService
{
    protected $questionRepository;
    protected $mcqQuestionRepository;
    protected $normalTextQuestionRepository;
    protected $creativeQuestionRepository;

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

}
