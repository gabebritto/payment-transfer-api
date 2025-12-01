<?php

namespace App\Modules\Transfer\Contracts;

interface NotificationStrategyInterface
{
    public function send(array $data, string $message): bool;
}
