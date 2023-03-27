<?php

namespace App\APIs;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CovidVaccineAPI
{
    protected array $BRANDS = [
        1 => 'AstraZeneca',
        5 => 'Moderna',
        6 => 'Pfizer',
        7 => 'Sinovac',
        8 => 'Sinopharm',
        11 => 'COVOVAX',
    ];

    public function __invoke(int|string $cid, $raw = false): array
    {
        try {
            $result = Http::asJson()
                ->retry(3, 200)
                ->post(config('covid.vaccine_url'), ['cid' => (string) $cid])
                ->json();
        } catch (Exception $e) {
            Log::notice('get_vaccine_api@'.$e->getMessage());

            return ['ok' => false, 'serverError' => false, 'message' => $e->getMessage()];
        }

        if (!$result || !isset($result['MessageCode'])) {
            return ['ok' => false, 'serverError' => true];
        }

        if ($result['MessageCode'] !== 200) {
            return ['ok' => true, 'found' => false, 'message' => $result['Message']];
        }

        $response = [
            'ok' => true,
            'found' => true,
            'data' => $result['result'],
        ];

        $response = $response['data'];

        if (($response['vaccine_history_count'] ?? null) === 0) {
            return ['ok' => true, 'found' => true, 'vaccinations' => []];
        }

        if ($raw) {
            return ['ok' => true, 'found' => true, 'vaccinations' => $response['vaccine_history']];
        }

        $vaccinations = [];
        foreach ($response['vaccine_history'] as $vac) {
            $date = explode('T', $vac['immunization_datetime'])[0];
            $vaccinations[] = [
                'brand' => $this->BRANDS[$vac['vaccine_manufacturer_id']] ?? 'no code',
                'label' => $vac['vaccine_name'],
                'date' => $date,
                'date_label' => now()->create($date)->format('d/m/Y'),
                'place' => $vac['hospital_name'],
                'manufacturer_id' => $vac['vaccine_manufacturer_id'],
            ];
        }

        return ['ok' => true, 'found' => true, 'vaccinations' => $vaccinations];
    }

}
