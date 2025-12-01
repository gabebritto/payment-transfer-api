<?php

namespace App\Enums;

enum TransactionEnum: string
{
    case COMPLETED = 'completed';
    case PENDING = 'pending';
    case FAILED = 'failed';
}
