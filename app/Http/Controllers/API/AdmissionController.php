<?php

namespace App\Http\Controllers\API;

use App\Contracts\PatientAPI;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    public function __invoke(Request $request, PatientAPI $api)
    {
        $validated = $request->validate([
            'an' => ['required', 'digits:8'],
        ]);

        $withSensitiveInfo = $request->route()->getName() === 'api.admission-with-sensitive-data';

        return $api->getAdmission($validated['an'], $withSensitiveInfo);
    }
}
