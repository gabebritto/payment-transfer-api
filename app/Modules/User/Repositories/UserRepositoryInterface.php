<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;
}
