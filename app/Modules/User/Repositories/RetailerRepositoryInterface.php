<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\Retailer;

interface RetailerRepositoryInterface
{
    public function create(array $data): Retailer;
}
