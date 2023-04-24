<?php

namespace App\Http\Controllers\API;

use App\Contracts\UserAPI;
use App\Http\Controllers\Controller;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class AuthenticateUserController extends Controller
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request, UserAPI $api)
    {
        $validated = $request->validate([
            'login' => ['required', 'regex:/^[a-zA-Z]{1,24}\.[a-zA-Z]{3}$/'],
            'password' => ['required', 'string'],
        ]);

        $withSensitiveInfo = $request->route()->getName() === 'api.authenticate-with-sensitive-data';

        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            true,
        );

        return $api->authenticate($validated['login'], $validated['password'], $withSensitiveInfo);
    }
}
