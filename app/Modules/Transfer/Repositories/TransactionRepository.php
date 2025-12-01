<?php

namespace App\Modules\Transfer\Repositories;

use App\Modules\Transfer\Models\Transaction;
use App\Modules\Transfer\Repositories\TransactionRepositoryInterface;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }
}
