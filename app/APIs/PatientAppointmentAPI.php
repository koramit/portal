<?php

namespace App\APIs;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PatientAppointmentAPI
{
    public function __invoke(string $keyName, string $keyValue, string $dateStart, ?string $dateEnd): array
    {
        $body = [];
        if ($keyName === 'hn') {
            $body['subject'] = "HN$keyValue";
        } elseif ($keyName === 'clinic') {
            $body['service-type-reference'] = $keyValue;
        } elseif ($keyName === 'doctor') {
            $body['actor'] = $keyValue;
        }

        $body['date'] = $dateEnd
            ? ["ge$dateStart", "le$dateEnd"]
            : "eq$dateStart";

        try {
            $response = Http::withOptions(['verify' => false])
                ->get(config('si_dsl.proxy_url'), [
                    'url' => config('si_dsl.appointment_endpoint'),
                    'headers' => config('si_dsl.headers_fhir'),
                    'body' => $body,
                ]);
        } catch (Exception $e) {
            Log::error('patient-appointment@'.$e->getMessage());

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
}
