<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait ADFSTokenAuthenticable
{
    protected function manageADFSToken(string $cacheKey, string $clientId, string $clientSecret): ?string
    {
        if ($token = cache($cacheKey)) {

            return $token;
        }

        try {
            $token = Http::asForm()
                ->withOptions(['verify' => false])
                ->retry(3, 200)
                ->post(config('siad.adfs_auth_url'), [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'grant_type' => 'client_credentials',
                ])->json()['access_token'];

            cache()->put($cacheKey, $token, now()->addHour());
        } catch (Exception $e) {
            Log::error("$cacheKey@".$e->getMessage());
            $token = null;
        }

        return $token;
    }
}
