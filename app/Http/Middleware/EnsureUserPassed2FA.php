<?php

namespace App\Http\Middleware;

use App\Services\TwoFactorService;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserPassed2FA
{
    /**
     * @throws ConnectionException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->notifiable && session('2fa_passed') !== true) {
            (new TwoFactorService($request->user()))->notify();

            return redirect()->route('2fa');
        }

        return $next($request);
    }
}
