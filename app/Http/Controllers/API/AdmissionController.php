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

        $data = $api->getAdmission($validated['an'], str_contains($request->route()->getName(), 'with-sensitive-data'));
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }
}
