<?php

namespace App\Http\Controllers\API;

use App\APIs\PatientFHIR;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class DSLPatientAdmissionController
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request, PatientFHIR $api)
    {
        $validated = $request->validate([
            'hn' => ['required', 'digits:8'],
            'raw' => ['sometimes', 'bool'],
        ]);

        $data = $api->getPatientAdmissions(
            $validated['hn'],
            $validated['raw'] ?? false,
            str_contains($request->route()->getName(), 'with-sensitive-data')
        );

        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }
}
