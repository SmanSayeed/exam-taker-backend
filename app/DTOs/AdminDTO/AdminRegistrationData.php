<?php

namespace App\DTOs\AdminDTO;

use Spatie\LaravelData\Data;

class AdminRegistrationData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $password_confirmation,
        public string $role
    ) {}
}
