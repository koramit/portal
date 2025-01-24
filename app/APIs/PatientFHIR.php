<?php

namespace App\APIs;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PatientFHIR
{

    public function getPatient(string $keyName, string $keyValue, bool $raw): array
    {
        $identifier = match ($keyName) {
            'hn' => "http://si.mahidol.ac.th/eHIS/MP_PATIENT|$keyValue",
            'cid' => "https://terms.sil-th.org/id/th-cid|$keyValue",
            'passport' => "https://terms.sil-th.org/id/passport-number|$keyValue",
            default => null,
        };

        try {
            $response = Http::withOptions(['verify' => false])
                ->get(config('si_dsl.proxy_url'), [
                    'url' => config('si_dsl.patient_endpoint'),
                    'headers' => config('si_dsl.headers'),
                    'body' => ['identifier' => $identifier],
                ]);
        } catch (Exception $e) {
            Log::error('patient-fhir@'.$e->getMessage());

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
        if ($response['total'] === 0) {
            return [
                'ok' => true,
                'found' => false,
            ];
        }

        if ($raw) {
            return [
                'ok' => true,
                'found' => true,
                'response' => $response,
            ];
        }

        return [
            'ok' => true,
            'found' => true,
        ];
    }
}
