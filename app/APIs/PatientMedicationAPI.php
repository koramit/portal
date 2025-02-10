<?php

namespace App\APIs;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PatientMedicationAPI
{
    public function __invoke(array $validated): array
    {
        $body = $this->getBody($validated);

        try {
            $response = Http::withOptions(['verify' => false])
                ->get(config('si_dsl.proxy_url'), [
                    'url' => config('si_dsl.medication_endpoint'),
                    'headers' => config('si_dsl.headers_fhir'),
                    'body' => $body,
                ]);
        } catch (Exception $e) {
            Log::error('patient-medication@'.$e->getMessage());

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
        if (array_key_exists('encounter', $validated)) {
            return ['encounter' => $validated['encounter']];
        }

        if (array_key_exists('limit', $validated)) {
            Validator::validate($validated, [
                'limit' => 'required',
                'hn' => 'required',
                'category' => 'required',
                'asc' => 'required',
            ]);

            return [
                'category' => strtoupper($validated['category']),
                'subject' => 'HN'.$validated['hn'],
                '_maxresults' => (int) $validated['limit'],
                '_sort' => $validated['asc'] ? 'whenHandOver' : '-whenHandOver',
            ];
        }

        Validator::validate($validated, [
            'hn' => 'required',
            'category' => 'required',
            'date_start' => 'required',
            'date_end' => 'sometimes',
        ]);

        $body = [
            'category' => strtoupper($validated['category']),
            'subject' => 'HN'.$validated['hn'],
        ];

        $body['whenhandover'] = ! array_key_exists('date_end', $validated)
            ? 'eq'.$validated['date_start']
            : ['ge'.$validated['date_start'], 'le'.$validated['date_end']];

        return $body;
    }
}
