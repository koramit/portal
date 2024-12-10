<?php

namespace App\Http\Controllers\API;

use App\Contracts\UserAPI;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class AuthenticateUserADFSController
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request, UserAPI $api)
    {
        $validated = $request->validate([
            'login' => ['required', config('siad.login_format_validate_rule')],
            'password' => ['required', 'string'],
        ]);

        $data = $api->authenticateADFS($validated['login'], $validated['password']);
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }
}
