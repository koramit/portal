<?php

namespace App\APIs;

use App\Traits\ADFSTokenAuthenticable;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PatientAllergyAPI implements \App\Contracts\PatientAllergyAPI
{
    use ADFSTokenAuthenticable;

    private ?string $API_TOKEN;

    public function __construct()
    {
        $this->API_TOKEN = $this->manageADFSToken();
    }

    public function __invoke(int|string $hn): array
    {
        $body = ['patient.identifier' => "http://si.mahidol.ac.th/eHIS/MP_PATIENT|$hn"];

        try {
            $response = Http::withToken($this->API_TOKEN)
                ->withOptions(['verify' => false])
                ->get(config('si_dsl.allergy_endpoint'), $body);
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

        $response = $response->json();

        return [
            'ok' => true,
            'found' => $response['total'] > 0,
            'response' => $response,
        ];
    }
}
