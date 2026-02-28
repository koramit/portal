<?php

namespace App\Http\Controllers\API;

use App\APIs\EncounterAPI;
use App\APIs\PatientAPI;
use App\Http\Controllers\Controller;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;
use Throwable;

class PatientRecentlyAdmissionEncounterController extends Controller
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'hn' => ['required', 'digits:8'],
        ]);

        $withSensitiveInfo = str_contains($request->route()->getName(), 'with-sensitive-data');

        $data = $this->getRecentlyAdmission($validated['hn'], $withSensitiveInfo);
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }

    protected function getRecentlyAdmission(string|int $hn, bool $withSensitiveInfo): array
    {
        $admissions = (new EncounterAPI)([
            'request' => [
                'class' => 'IMP',
                'subject' => 'HN'.$hn,
                '_sort' => '-date',
                '_maxresults' => 1,
            ],
        ]);

        if (! $admissions['ok'] || ! $admissions['found']) {
            return $admissions;
        }

        $an = str_replace('AN', '', $admissions['response']['entry'][0]['resource']['id']);

        try {
            return (new PatientAPI)->getAdmission($an, $withSensitiveInfo);
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'found' => false,
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }

    }
}
