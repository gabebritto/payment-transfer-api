<?php

namespace App\Modules\Transfer\Clients;

use Illuminate\Support\Facades\Http;

class EmailNotificationClient
{
    protected string $baseUrl = 'https://util.devi.tools/api/v1/notify';

    public function notify(string $email, string $message): bool
    {
        $response = Http::post($this->baseUrl, [
            'email' => $email,
            'message' => $message,
        ]);

        return $response->successful();
    }
}
