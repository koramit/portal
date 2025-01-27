<?php

namespace App\Http\Controllers\API;

use App\APIs\PatientAppointmentAPI;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class PatientAppointmentController
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request, PatientAppointmentAPI $api)
    {
        $validated = $request->validate([
            'key_name' => ['required', 'in:hn,clinic,doctor'],
            'key_value' => ['required', 'string', 'max:24'],
            'date_start' => ['required', 'date'],
            'date_end' => ['sometimes', 'date'],
        ]);

        $data = $api(
            $validated['key_name'],
            $validated['key_value'],
            $validated['date_start'],
            $validated['date_end'] ?? null
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
