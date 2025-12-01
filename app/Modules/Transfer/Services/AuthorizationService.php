<?php

namespace App\Modules\Transfer\Services;

class AuthorizationService
{
    public function __construct(
        protected \App\Modules\Transfer\Clients\AuthorizationClient $client
    ) {}

    public function isAuthorized(): bool
    {
        try {
            return $this->client->authorize() ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
