<?php

namespace App\Http\Controllers\API;

use App\APIs\PatientFHIR;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class DSLPatientController
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request, PatientFHIR $api)
    {
        $validated = $request->validate([
            'key_name' => ['required', 'in:hn,cid,passport'],
            'key_value' => ['required', 'alpha_num', 'max:13'],
            'raw' => ['sometimes', 'bool'],
        ]);

        $data = $api->getPatient(
            $validated['key_name'],
            $validated['key_value'],
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
