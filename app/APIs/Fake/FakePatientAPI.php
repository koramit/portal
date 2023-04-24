<?php

namespace App\APIs\Fake;

use App\Contracts\AdmissionAPI;
use App\Contracts\PatientAPI;

class FakePatientAPI implements PatientAPI, AdmissionAPI
{

    public function getPatient(int $hn, bool $withSensitiveInfo): array
    {
        // TODO: Implement getAdmission() method.
        return [
            'ok' => true,
            'found' => false,
        ];
    }

    public function getAdmission(int $an, bool $withSensitiveInfo): array
    {
        // TODO: Implement getAdmission() method.
        return [
            'ok' => true,
            'found' => false,
        ];
    }

    public function getPatientAdmissions(int $hn, bool $withSensitiveInfo): array
    {
        // TODO: Implement getPatientAdmissions() method.
        return [
            'ok' => true,
            'found' => false,
        ];
    }

    public function getPatientRecentlyAdmission(int $hn, bool $withSensitiveInfo): array
    {
        // TODO: Implement getPatientRecentlyAdmission() method.
        return [
            'ok' => true,
            'found' => false,
        ];
    }
}
