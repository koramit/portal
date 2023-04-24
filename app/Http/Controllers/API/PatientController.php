<?php

namespace App\Http\Controllers\API;

use App\Contracts\AdmissionAPI;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __invoke(Request $request, AdmissionAPI $api)
    {
        $validated = $request->validate([
            'hn' => ['required', 'digits:8'],
        ]);

        $withSensitiveInfo = $request->route()->getName() === 'api.patient-with-sensitive-data';

        return $api->getPatient($validated['hn'], $withSensitiveInfo);
    }
}
