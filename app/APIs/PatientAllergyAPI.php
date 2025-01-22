<?php

namespace App\APIs;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PatientAllergyAPI implements \App\Contracts\PatientAllergyAPI
{

    public function __invoke(int|string $hn): array
    {
        try {
            $response = Http::withOptions(['verify' => false])
                ->get(config('si_dsl.proxy_url'), [
                    'url' => config('si_dsl.allergy_endpoint'),
                    'headers' => config('si_dsl.headers'),
                    'body' => ['patient.identifier' => "http://si.mahidol.ac.th/eHIS/MP_PATIENT|$hn"],
                ]);
        } catch (Exception $e) {
            Log::error('patient-allergy@'.$e->getMessage());

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

        return [
            'ok' => true,
            'found' => true,
            'response' => $response->json(),
        ];
    }
}
