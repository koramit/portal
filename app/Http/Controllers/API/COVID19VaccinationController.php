<?php

namespace App\Http\Controllers\API;

use App\Contracts\COVID19VaccinationAPI;
use App\Http\Controllers\Controller;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class COVID19VaccinationController extends Controller
{
    use ServiceAccessLoggable;
    public function __invoke(Request $request, COVID19VaccinationAPI $api)
    {
        $validated = $request->validate([
            'cid' => ['required', 'digits_between:12,13'],
        ]);

        $data = $api($validated['cid']);
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }
}
