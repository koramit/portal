<?php

namespace App\Http\Controllers\API;

use App\Contracts\PatientAPI;
use App\Http\Controllers\Controller;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    use ServiceAccessLoggable;
    public function __invoke(Request $request, PatientAPI $api)
    {
        $validated = $request->validate([
            'an' => ['required', 'digits:8'],
        ]);

        $withSensitiveInfo = $request->route()->getName() === 'api.admission-with-sensitive-data';
        $data = $api->getAdmission($validated['an'], $withSensitiveInfo);
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }
}
