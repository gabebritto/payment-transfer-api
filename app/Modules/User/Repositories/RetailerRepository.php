<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\Retailer;

class RetailerRepository implements RetailerRepositoryInterface
{
    public function create(array $data): Retailer
    {
        return Retailer::create($data);
    }
}
