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
            'login' => ['required', config('siad.login_format_validate_rule')],
            'password' => ['required', 'string'],
        ]);

        $data = $api->authenticate(
            $validated['login'],
            $validated['password'],
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
}
