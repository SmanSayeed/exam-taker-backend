<?php

namespace App\DTOs\Admin\Auth;

use Spatie\LaravelData\Data;

class AdminRegistrationData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $role
    ) {}
}
