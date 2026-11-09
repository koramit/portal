<?php

namespace App\Http\Controllers\API;

use App\Contracts\LabAPI;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class LabResultController
{
    use ServiceAccessLoggable;

    public function fromRefNo(Request $request, LabAPI $api)
    {
        $validated = $request->validate([
            'ref_no' => ['required', 'string'],
            'raw' => ['sometimes', 'boolean'],
        ]);

        $data = $api->getLabFromRefNo($validated['ref_no'], $validated['raw'] ?? false);
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }

    public function fromServiceId(Request $request, LabAPI $api)
    {
        $validated = $request->validate([
            'hn' => ['required', 'digits:8'],
            'service_ids' => ['required', 'array'],
            'latest' => ['nullable', 'boolean'],
            'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'date'],
            'raw' => ['sometimes', 'boolean'],
        ]);

        $data = $api->getLabFromServiceId($validated);
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }

    public function fromItemCode(Request $request, LabAPI $api)
    {
        $validated = $request->validate([
            'hn' => ['required', 'digits:8'],
            'item_codes' => ['required', 'array'],
            'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'date'],
            'raw' => ['sometimes', 'boolean'],
        ]);

        $data = $api->getLabFromItemCode($validated);
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }

    public function fromItemCodeAllResult(Request $request, LabAPI $api)
    {
        $validated = $request->validate([
            'hn' => ['required', 'digits:8'],
            'item_code' => ['required', 'string'],
            'raw' => ['sometimes', 'boolean'],
        ]);

        $data = $api->getLabFromItemCodeAllResults($validated);
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }

    public function forwardRequest(Request $request, LabAPI $api)
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
            'body' => ['required', 'array'],
        ]);

        $data = $api->forwardRequest($validated);
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }
}
