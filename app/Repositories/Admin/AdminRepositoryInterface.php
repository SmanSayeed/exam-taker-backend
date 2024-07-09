<?php

namespace App\Repositories;

use App\Models\Admin;

interface AdminRepositoryInterface
{
    public function create(array $data): Admin;
    public function findByEmail(string $email): ?Admin;
}
