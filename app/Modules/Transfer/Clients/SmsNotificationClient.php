<?php

namespace App\Modules\Transfer\Clients;

use Illuminate\Support\Facades\Http;

class SmsNotificationClient
{
    protected string $baseUrl = 'https://util.devi.tools/api/v1/notify';

    public function notify(string $phoneNumber, string $message): bool
    {
        $response = Http::post($this->baseUrl, [
            'phone_number' => $phoneNumber,
            'message' => $message,
        ]);

        return $response->successful();
    }
}
