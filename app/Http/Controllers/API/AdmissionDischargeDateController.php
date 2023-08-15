<?php

namespace App\Http\Controllers\API;

use App\APIs\WardAPI;
use App\Http\Controllers\Controller;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class AdmissionDischargeDateController extends Controller
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request, WardAPI $api)
    {
        $validated = $request->validate([
            'begin_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'number' => ['nullable', 'exists:wards,id'],
        ]);

        $data = $api->getAdmissionDischargeDate($validated);
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            true,
        );

        return $data;
    }
}
