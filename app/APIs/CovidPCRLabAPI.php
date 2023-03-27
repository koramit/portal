<?php

namespace App\APIs;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CovidPCRLabAPI
{
    protected ?string $API_TOKEN; // expires in 1 hour
    protected string $TOKEN_CACHE_KEY = 'covid-pcr-lab-token';

    protected array $COVID_LAB_CODES = ['204592', '204593', '5565'];

    protected int $START_DATE_OFFSET = 90;

    // @TODO check another result label ie positive, negative, etc.
    protected array $RESULTS = ['detected', 'not detected', 'inconclusive', 'invalid'];

    public function __construct()
    {
        if ($token = cache($this->TOKEN_CACHE_KEY)) {
            $this->API_TOKEN = $token;
            return;
        }

        try {
            $this->API_TOKEN = Http::asForm()
                ->withOptions(['verify' => false])
                ->retry(3, 200)
                ->post(config('covid.auth_url'), [
                    'client_id' => config('covid.id'),
                    'client_secret' => config('covid.secret'),
                    'grant_type' => 'client_credentials'
                ])->json()['access_token'];

            cache()->put($this->TOKEN_CACHE_KEY, $this->API_TOKEN, now()->addHour());
        } catch (Exception $e) {
            Log::notice('get_covid_pcr_lab_api_token@'.$e->getMessage());
            $this->API_TOKEN = null;
        }
    }

    public function getLabs(int|string $hn, string $dateLab): array
    {
        $form = [
            'HN' => (string) $hn,
            'GROUP' => true,
            'GROUP_SERVICE_ID' => $this->COVID_LAB_CODES,
            'START_DATE' => Carbon::create($dateLab)->subDays($this->START_DATE_OFFSET)->format('Y-m-d'),
            'END_DATE' => Carbon::create($dateLab)->addDay()->format('Y-m-d'),
        ];

        try {
            $result = Http::withToken($this->API_TOKEN)
                ->withOptions(['verify' => false])
                ->retry(3, 200)
                ->post(config('covid.service_url'), $form)
                ->json();
        } catch (Exception $e) {
            Log::notice('get_covid_pcr_lab_result@'.$e->getMessage());

            return [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }

        if ($result === null) {
            return ['ok' => false, 'serverError' => true];
        }

        if ($result === []) {
            return [
                'ok' => true,
                'found' => false,
            ];
        }

        $labs = collect($result)->transform(function ($lab) {
            $results = collect($lab['RESULT']);
            $foundIndex = collect($results)->search(
                fn ($l) => collect($this->COVID_LAB_CODES)->contains($l['TI_CODE'])
                    && collect($this->RESULTS)->contains(strtolower($l['RESULT_CHAR'] ?? ''))
            );
            if ($foundIndex === false) {
                return [
                    'date_lab' => $lab['REPORT_DATE'],
                    'note' => $lab['NOTE'] ?? null,
                    'result' => 'pending?',
                ];
            }
            return [
                'date_lab' => $lab['REPORT_DATE'],
                'note' => $lab['NOTE'] ?? null,
                'code' => $results[$foundIndex]['TI_CODE'],
                'name' => $results[$foundIndex]['TI_NAME'],
                'result' => $results[$foundIndex]['RESULT_CHAR'],
            ];
        });

        return [
            'ok' => true,
            'found' => true,
            'labs' => $labs
        ];
    }
}
