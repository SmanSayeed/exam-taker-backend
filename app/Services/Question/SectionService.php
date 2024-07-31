<?php
namespace App\Services;

use App\Models\Section;
use Illuminate\Support\Facades\DB;
use Exception;

class SectionService
{
    public function createSection(array $data)
    {
        return Section::create($data);
    }

    public function updateSection(Section $section, array $data)
    {
        $section->update($data);
        return $section;
    }

    public function deleteSection(Section $section)
    {
        $section->delete();
    }

    public function changeStatus(Section $section, bool $status)
    {
        $section->status = $status;
        $section->save();
        return $section;
    }
}
