<?php

namespace App\Modules\Transfer\Clients;

use Illuminate\Support\Facades\Http;

class AuthorizationClient
{
    protected string $baseUrl = 'https://util.devi.tools/api/v2/authorize';

    public function authorize(): bool
    {
        $response = Http::get($this->baseUrl);

        if ($response->successful()) {
            $data = $response->json();

            return $data['authorization'];
        }

        return false;
    }
}
