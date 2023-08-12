<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterUserController extends Controller
{
    public function create()
    {
        return view('auth.register', ['title' => 'Register']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'unique:users,name'],
            'full_name' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()],
        ]);

        $user = User::query()
            ->create([
                'name' => $validated['name'],
                'full_name' => $validated['full_name'],
                'password' => Hash::make($validated['password']),
                'expire_at' => now()->addYear(),
            ]);

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
