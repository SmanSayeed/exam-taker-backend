<?php
namespace App\Services\Question;

use App\DTOs\QuestionDTO\SectionData;
use App\Models\Section;
use App\Repositories\QuestionRepository\SectionRepositoryInterface;
use Exception;

class SectionService
{
    protected SectionRepositoryInterface $sectionRepository;

    public function __construct(SectionRepositoryInterface $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }

    public function createSection(SectionData $data): Section
    {
        return $this->sectionRepository->create($data->toArray());
    }

    public function updateSection(Section $section, SectionData $data): Section
    {
        return $this->sectionRepository->update($section, $data->toArray());
    }

    public function deleteSection(Section $section): void
    {
        $this->sectionRepository->delete($section);
    }

    public function changeStatus(Section $section, bool $status): Section
    {
        return $this->sectionRepository->changeStatus($section, $status);
    }

    public function getAllSections(): array
    {
        return $this->sectionRepository->getAllSections();
    }
}
