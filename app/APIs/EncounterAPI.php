<?php

namespace App\APIs;

use App\Traits\ADFSTokenAuthenticable;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EncounterAPI
{
    use ADFSTokenAuthenticable;

    private ?string $API_TOKEN;

    public function __construct()
    {
        $this->API_TOKEN = $this->manageADFSToken();
    }

    public function __invoke(array $validated): array
    {
        $body = $this->getBody($validated);

        $url = config('si_dsl.encounter_endpoint').'?'.urldecode(http_build_query($body));
        $url = preg_replace('/\[\d+]/', '', $url);

        try {
            $response = Http::withToken($this->API_TOKEN)
                ->withOptions(['verify' => false])
                ->get($url);
        } catch (Exception $e) {
            Log::error('encounter@'.$e->getMessage());

            return [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }

        if ($response->status() !== 200) {
            return [
                'ok' => true,
                'found' => false,
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        $response = $response->json();

        return [
            'ok' => true,
            'found' => $response['total'] > 0,
            'response' => $response,
        ];
    }

    protected function getBody(array $validated): array
    {
        if (array_key_exists('request', $validated)) {
            return $validated['request'];
        }

        if (array_key_exists('id', $validated)) {
            return ['identifier' => $validated['id']];
        }

        if (array_key_exists('part_of', $validated)) {
            return ['part-of.identifier' => $validated['part_of']];
        }

        Validator::validate($validated, ['hn' => 'required']);
        $body['subject'] = 'HN'.$validated['hn'];

        $dateStart = $validated['date_start'] ?? null;
        $dateEnd = $validated['date_end'] ?? null;

        if ($dateStart && $dateEnd) {
            $body['date'] = ['ge'.$dateStart, 'le'.$dateEnd];
        } elseif ($dateStart) {
            $body['date'] = 'eq'.$dateStart;
        }

        if (array_key_exists('status', $validated)) {
            $body['status'] = $validated['status'];
        }

        return $body;
    }
}
