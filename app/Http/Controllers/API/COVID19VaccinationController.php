<?php

namespace App\Http\Controllers\API;

use App\Contracts\COVID19VaccinationAPI;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class COVID19VaccinationController extends Controller
{
    public function __invoke(Request $request, COVID19VaccinationAPI $api)
    {
        $validated = $request->validate([
            'cid' => ['required', 'digits_between:12,13'],
        ]);

        return $api($validated['cid']);
    }
}
