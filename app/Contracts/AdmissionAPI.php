<?php

namespace App\Contracts;

interface AdmissionAPI
{
    public function getAdmission(int $an, bool $withSensitiveInfo): array;

    public function getPatientAdmissions(int $hn, bool $withSensitiveInfo): array;

    public function getPatientRecentlyAdmission(int $hn, bool $withSensitiveInfo): array;
}
