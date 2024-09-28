<?php

namespace App\DTOs\StudentDTO;

use Spatie\LaravelData\Data;

class StudentStatusChangeData extends Data
{
    public function __construct(
        public bool $active_status
    ) {
    }

    /**
     * Custom method to convert DTO to array
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->toArray();
    }
}
