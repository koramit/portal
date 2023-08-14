<?php

namespace App\Http\Controllers\API;

use App\APIs\WardAPI;
use App\Http\Controllers\Controller;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class WardAdmissionController extends Controller
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request, WardAPI $api)
    {
        $validated = $request->validate([
            'number' => ['nullable', 'exists:wards,id'],
        ]);

        $data = $api->getWard($validated['number'] ?? null);
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            true,
        );

        return $data;
    }
}
