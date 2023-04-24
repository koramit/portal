<?php

namespace App\APIs\Fake;

use App\Contracts\COVID19VaccinationAPI;

class FakeCOVID19VaccinationAPI implements COVID19VaccinationAPI
{
    public function __invoke(int|string $cid, $raw = false): array
    {
        // TODO: Implement __invoke() method.
        return [
            'ok' => true,
            'found' => false,
        ];
    }
}
