<?php

namespace App\Http\Controllers\API;

use App\Contracts\LabAPI;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class LabRecentlyOrderController
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request, LabAPI $api)
    {
        $validated = $request->validate([
            'hn' => ['required', 'digits:8'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'raw' => ['sometimes', 'boolean'],
        ]);

        $data = $api->getLabRecentlyOrders(
            $validated['hn'],
     $validated['limit'] ?? 10,
            $validated['raw'] ?? false
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
