<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    const MAX_ATTEMPTS_PER_MINUTE = 3;

    const USERNAME = 'name';

    public function create()
    {
        return view('auth.login', ['title' => 'Login']);
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            self::USERNAME => 'required|string',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials[self::USERNAME]).'|'.$request->ip());
        $this->ensureIsNotRateLimited($throttleKey);

        if (Auth::attempt($credentials)) {
            // @TODO: should notify user
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);

            return redirect()->intended(RouteServiceProvider::HOME);
        }

        RateLimiter::hit($throttleKey);
        throw ValidationException::withMessages([
            self::USERNAME => trans('auth.failed'),
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function ensureIsNotRateLimited(string $throttleKey): void
    {
        if (! RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS_PER_MINUTE)) {
            return;
        }

        // @TODO: should report or keep log
        $seconds = RateLimiter::availableIn($throttleKey);

        throw ValidationException::withMessages([
            self::USERNAME => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }
}
