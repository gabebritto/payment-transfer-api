<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        return User::create($data);
    }
}
