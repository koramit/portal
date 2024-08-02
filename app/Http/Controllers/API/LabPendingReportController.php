<?php

namespace App\Http\Controllers\API;

use App\Contracts\LabAPI;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class LabPendingReportController
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request, LabAPI $api)
    {
        $validated = $request->validate([
            'hn' => ['required', 'digits:8'],
        ]);

        $data = $api->getLabPendingReports($validated['hn']);
        /*$this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );*/

        return $data;
    }
}
