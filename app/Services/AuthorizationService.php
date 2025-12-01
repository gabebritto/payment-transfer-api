<?php

namespace App\Services;

class AuthorizationService
{
    public function __construct(
        protected \App\Clients\AuthorizationClient $client
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
