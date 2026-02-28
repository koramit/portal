<?php

namespace App\Http\Controllers\API;

use App\APIs\PatientFHIR;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class DSLAdmissionController
{
    use ServiceAccessLoggable;

    public function show(Request $request, PatientFHIR $api)
    {
        $validated = $request->validate([
            'an' => ['required', 'digits:8'],
            'raw' => ['sometimes', 'bool'],
        ]);

        $data = $api->getAdmission(
            $validated['an'],
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

    public function index(Request $request, PatientFHIR $api)
    {
        $validated = $request->validate([
            'date_ref' => ['required', 'date'],
            'page_no' => ['required', 'integer'],
            'items_per_page' => ['sometimes', 'integer'],
        ]);

        $data = $api->getAdmissionPagination($validated['date_ref'], $validated['page_no'], $validated['items_per_page'] ?? 15);
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }
}
