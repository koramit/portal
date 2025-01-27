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

        $routeName = $request->route()->getName();
        $withSensitiveInfo = $routeName === 'api.dsl.patient-admissions-with-sensitive-data';
        $data = $api->getPatientAdmissions($validated['hn'], $validated['raw'] ?? false, $withSensitiveInfo);
        $this->log(
            $request->bearerToken(),
            $validated,
            $routeName,
            $data['found'] ?? false,
        );

        return $data;
    }
}
