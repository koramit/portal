<?php

namespace App\APIs;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ItemizeAPI
{
    protected ?string $API_TOKEN; // expires in 1 hour

    protected string $TOKEN_CACHE_KEY = 'item-master-auth-token';

    public function __construct()
    {
        if ($token = cache($this->TOKEN_CACHE_KEY)) {
            $this->API_TOKEN = $token;

            return;
        }

        try {
            $this->API_TOKEN = Http::asForm()
                ->retry(3, 200)
                ->post(config('itemize.auth_url'), [
                    'client_id' => config('itemize.id'),
                    'client_secret' => config('itemize.secret'),
                    'grant_type' => 'client_credentials',
                ])->json()['access_token'];

            cache()->put($this->TOKEN_CACHE_KEY, $this->API_TOKEN, now()->addHour());
        } catch (Exception $e) {
            Log::notice('get_item_master_api_token@'.$e->getMessage());
            $this->API_TOKEN = null;
        }
    }

    public function getItem(string $category, string $search = '', string $itemStatus = 'ALL'): array
    {
        $form = match ($category) {
            'drug', 'supply' => ['search_text' => $search, 'item_status' => $itemStatus],
            'department', 'doctor', 'title' => ['date_in' => now()->firstOfYear()->format('Ymd')],
            default => [],
        };

        try {
            if (isset($form['date_in'])) {
                $result = Http::withToken($this->API_TOKEN)
                    ->retry(3, 200)
                    ->get(config('itemize.service_url').$category, $form)
                    ->json();
            } else {
                $result = Http::withToken($this->API_TOKEN)
                    ->retry(3, 200)
                    ->post(config('itemize.service_url').$category, $form)
                    ->json();
            }

        } catch (Exception $e) {
            Log::notice('get_'.$category.'_master_result@'.$e->getMessage());

            return [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }

        if (! isset($result[$category])) {
            return ['ok' => true, 'found' => false];
        }

        return [
            'ok' => true,
            'found' => true,
            'items' => $result[$category],
        ];
    }
}
