<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TwoFactorsChallengeController extends Controller
{
    public function create()
    {
        return view('auth.2fa')->with([
            'title' => '2FA Challenge',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            '2fa_code' => ['required', 'numeric'],
        ]);

        $manager = new TwoFactorService($request->user());
        if (! $manager->verify($validated['2fa_code'])) {
            throw ValidationException::withMessages([
                '2fa_code' => 'รหัสผ่านสองขั้นตอนไม่ถูกต้อง',
            ]);
        }

        session()->put('2fa_passed', true);

        return redirect()->intended(route('dashboard'));
    }
}
