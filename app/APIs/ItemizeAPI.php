<?php

namespace App\APIs;

use App\Traits\ADFSTokenAuthenticable;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ItemizeAPI
{
    use ADFSTokenAuthenticable;

    protected ?string $API_TOKEN; // expires in 1 hour

    protected string $TOKEN_CACHE_KEY = 'item-master-auth-token';

    public function __construct()
    {
        $this->API_TOKEN = $this->manageADFSToken(
            $this->TOKEN_CACHE_KEY,
            config('itemize.id'),
            config('itemize.secret'),
        );
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
                    ->withOptions(['verify' => false])
                    ->retry(3, 200)
                    ->get(config('itemize.service_url').$category, $form)
                    ->json();
            } else {
                $result = Http::withToken($this->API_TOKEN)
                    ->withOptions(['verify' => false])
                    ->retry(3, 200)
                    ->post(config('itemize.service_url').$category, $form)
                    ->json();
            }

        } catch (Exception $e) {
            Log::error('get_'.$category.'_master_result@'.$e->getMessage());

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
