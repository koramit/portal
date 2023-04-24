@extends('auth.layout')

@section('form')
<form
    class="mt-8 space-y-4"
    action="{{ route('login.store')  }}"
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
            autofocus
        />
        @error('name')
        <small class="text-red-500 text-sm">{{ $message }}</small>
        @enderror
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
        />
    </div>
    <div class="flex justify-between items-center">
        <a
            class="text-sm text-teal-500 hover:text-teal-700"
            href="{{ route('register') }}"
        >Register</a>
        <button
            class="btn-accent"
            type="submit"
        >LOGIN</button>
    </div>
</form>
@endsection
