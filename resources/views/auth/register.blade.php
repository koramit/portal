@extends('auth.layout')

@section('form')
<form
    class="mt-8 space-y-4"
    action="{{ route('register.store')  }}"
    method="POST"
>
    @csrf
    <div class="space-y-2">
        <label
            class="form-label"
            for="username"
        >Username :</label>
        <input
            class="form-input"
            name="name"
            id="username"
            value="{{ old('name') }}"
        />
        @error('name')
        <small class="text-red-500 text-sm">{{ $message }}</small>
        @enderror
    </div>
    <div class="space-y-2">
        <label
            class="form-label"
            for="full_name"
        >Full name :</label>
        <input
            class="form-input"
            name="full_name"
            id="full_name"
            value="{{ old('full_name') }}"
        />
    </div>
    <div class="space-y-2">
        <label
            class="form-label"
            for="password"
        >Password :</label>
        <input
            class="form-input"
            type="password"
            name="password"
            id="password"
            value="{{ old('password') }}"
        />
        @error('password')
        <small class="text-red-500 text-sm">{{ $message }}</small>
        @enderror
    </div>
    <div class="space-y-2">
        <label
            class="form-label"
            for="password_confirmation"
        >Confirm Password :</label>
        <input
            class="form-input"
            type="password"
            name="password_confirmation"
            id="password_confirmation"
            value="{{ old('password_confirmation') }}"
        />
    </div>
    <div class="flex justify-between items-center">
        <a
            class="text-sm text-teal-500 hover:text-teal-700"
            href="{{ route('login') }}"
        >Login</a>
        <button
            class="btn-accent"
            type="submit"
        >REGISTER</button>
    </div>
</form>
@endsection
