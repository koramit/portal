<?php

namespace App\Http\Controllers\API;

use App\Contracts\UserAPI;
use App\Http\Controllers\Controller;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class GetUserController extends Controller
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request, UserAPI $api)
    {
        $validated = $request->validate([
            'org_id' => ['nullable', 'digits:8', 'required_without_all:login'],
            'login' => ['nullable', 'regex:/^[a-zA-Z]{1,24}\.[a-zA-Z]{3}$/', 'required_without_all:org_id'],
            'with_sensitive_data' => ['nullable', 'boolean'],
        ]);

        $withSensitiveInfo = $request->route()->getName() === 'api.user-with-sensitive-data';

        if (isset($validated['org_id'])) {
            $data = $api->getUserById($validated['org_id'], $withSensitiveInfo);
        } else {
            $data = $api->getUserByLogin($validated['login'], $withSensitiveInfo);
        }

        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }
}
