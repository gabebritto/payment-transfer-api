<?php

namespace App\Modules\Transfer\Repositories;

use App\Modules\Transfer\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function create(array $data): Transaction;
}
