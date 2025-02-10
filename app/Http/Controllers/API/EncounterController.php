<?php

namespace App\Http\Controllers\API;

use App\APIs\EncounterAPI;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class EncounterController
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request, EncounterAPI $api)
    {
        $validated = $request->validate([
            'hn' => ['sometimes', 'digits:8'],
            'date_start' => ['sometimes', 'date'],
            'date_end' => ['sometimes', 'date'],
            'status' => ['sometimes', 'in:finished,cancelled,in-progress'],
            'id' => ['sometimes', 'url'],
            'part_of' => ['sometimes', 'url'],
        ]);

        $data = $api($validated);
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }
}
