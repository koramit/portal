<?php

namespace App\Contracts;

interface PatientAllergyAPI
{
    public function __invoke(int|string $hn): array;
}
