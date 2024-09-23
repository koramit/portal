<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\RootInitiateService;
use Illuminate\Http\Request;

class InitRootController extends Controller
{
    public function __invoke(int $code, Request $request)
    {
        $service = new RootInitiateService;
        if ($service->isRootInitiated()) {
            abort(403);
        }

        if (! $service->verifyCode($code)) {
            abort(403);
        }

        /** @var Role $root */
        $root = Role::query()->where('name', 'root')->first();
        $request->user()->attachRole($root);

        return redirect()->route('dashboard')->with(['status' => 'Root initiated']);
    }
}
