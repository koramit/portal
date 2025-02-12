<?php

namespace App\Http\Controllers\API;

use App\APIs\PatientMedicationAPI;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class PatientMedicationController
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request, PatientMedicationAPI $api)
    {
        $validated = $request->validate([
            'encounter' => ['sometimes', 'alpha_num', 'max:12'],
            'category' => ['sometimes', 'in:opd,ipd'],
            'date_start' => ['sometimes', 'date'],
            'date_end' => ['sometimes', 'date'],
            'hn' => ['sometimes', 'digits:8'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'asc' => ['sometimes', 'boolean'],
            'request' => ['sometimes', 'array'],
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
