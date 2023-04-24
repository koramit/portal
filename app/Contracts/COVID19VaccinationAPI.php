<?php

namespace App\Contracts;

interface COVID19VaccinationAPI
{
    public function __invoke(int|string $cid, $raw = false): array;
}
