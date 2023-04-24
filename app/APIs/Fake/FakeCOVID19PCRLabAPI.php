<?php

namespace App\APIs\Fake;

use App\Contracts\COVID19PCRLabAPI;

class FakeCOVID19PCRLabAPI implements COVID19PCRLabAPI
{
    public function __invoke(int|string $hn, string $dateLab): array
    {
        // TODO: Implement __invoke() method.
        return [
            'ok' => true,
            'found' => false,
        ];
    }
}
