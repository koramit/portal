<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLINENotifyIsEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->line_notify_enabled) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Please set LINE notify first']);
        }

        return $next($request);
    }
}
