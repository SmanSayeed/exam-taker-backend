<?php

namespace App\DTOs\AdminDTO;

use Spatie\LaravelData\Data;

class AdminLoginData extends Data
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}
