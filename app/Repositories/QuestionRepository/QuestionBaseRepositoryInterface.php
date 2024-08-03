<?php

namespace App\Repositories\QuestionRepository;

use Illuminate\Database\Eloquent\Model;

interface QuestionBaseRepositoryInterface
{
    public function getAll();
    public function findById(int $id);
    public function create(array $data): Model;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}

interface SectionRepositoryInterface extends QuestionBaseRepositoryInterface {}

interface ExamTypeRepositoryInterface extends QuestionBaseRepositoryInterface {}

interface ExamSubTypeRepositoryInterface extends QuestionBaseRepositoryInterface {}

interface YearRepositoryInterface extends QuestionBaseRepositoryInterface {}

interface GroupRepositoryInterface extends QuestionBaseRepositoryInterface {}

interface LevelRepositoryInterface extends QuestionBaseRepositoryInterface {}

interface SubjectRepositoryInterface extends QuestionBaseRepositoryInterface {}

interface LessonRepositoryInterface extends QuestionBaseRepositoryInterface {}

interface TopicRepositoryInterface extends QuestionBaseRepositoryInterface {}

interface SubTopicRepositoryInterface extends QuestionBaseRepositoryInterface {}
