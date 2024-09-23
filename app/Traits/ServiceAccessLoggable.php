<?php

namespace App\Traits;

use App\Models\PersonalAccessToken;

trait ServiceAccessLoggable
{
    public function log(string $tokenString, array $payload, string $route, bool $found): void
    {
        defer(function () use ($tokenString, $payload, $route, $found) {
            $token = PersonalAccessToken::findToken($tokenString);
            unset($payload['password']);
            $token->serviceAccessLogs()->create([
                'payload' => $payload,
                'route' => $route,
                'found' => $found,
            ]);
        });
    }
}
