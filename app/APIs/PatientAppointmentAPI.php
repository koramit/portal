<?php

namespace App\APIs;

use App\Traits\ADFSTokenAuthenticable;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PatientAppointmentAPI
{
    use ADFSTokenAuthenticable;

    private ?string $API_TOKEN;

    public function __construct()
    {
        $this->API_TOKEN = $this->manageADFSToken();
    }

    public function __invoke(string $keyName, string $keyValue, string $dateStart, ?string $dateEnd): array
    {
        $body = [];
        if ($keyName === 'hn') {
            $body['subject'] = "HN$keyValue";
        } elseif ($keyName === 'clinic') {
            $body['service-type-reference'] = $keyValue;
        } elseif ($keyName === 'doctor') {
            $body['actor'] = "DR$keyValue";
        }

        $body['date'] = $dateEnd
            ? ["ge$dateStart", "le$dateEnd"]
            : "eq$dateStart";

        $url = config('si_dsl.appointment_endpoint').'?'.urldecode(http_build_query($body));
        $url = preg_replace('/\[\d+]/', '', $url);

        try {
            $response = Http::withToken($this->API_TOKEN)
                ->withOptions(['verify' => false])
                ->get($url);
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
