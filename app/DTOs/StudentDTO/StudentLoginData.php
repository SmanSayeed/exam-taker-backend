<?php

namespace App\DTOs\StudentDTO;

use Spatie\LaravelData\Data;

class StudentLoginData extends Data
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}
