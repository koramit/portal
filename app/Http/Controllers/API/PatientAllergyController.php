<?php

namespace App\Http\Controllers\API;

use App\APIs\PatientAllergyAPI;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class PatientAllergyController
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request, PatientAllergyAPI $api)
    {
        $validated = $request->validate([
            'hn' => ['required', 'digits:8']
        ]);

        $data = $api($validated['hn']);
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }
}
