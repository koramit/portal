<?php

namespace App\Contracts;

interface PatientAPI
{
    public function getPatient(int $hn, bool $withSensitiveInfo): array;
}
