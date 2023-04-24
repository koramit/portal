<?php

namespace App\Http\Controllers\API;

use App\APIs\CovidPCRLabAPI;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class COVID19PCRLabController extends Controller
{
    public function __invoke(Request $request, CovidPCRLabAPI $api)
    {
        $validated = $request->validate([
            'hn' => ['required', 'digits:8'],
            'date_lab' => ['required', 'date_format:Y-m-d'],
        ]);

        return $api($validated['hn'], $validated['date_lab']);
    }
}
