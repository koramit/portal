@extends('layout')

@section('content')
    <div class="flex flex-col w-full min-h-screen justify-center items-center bg-slate-50">
        <div class="-mt-8 mx-auto w-96 md:w-1/3 p-8 md:p-12">
            <form
                method="POST"
                action="{{ route('2fa.store') }}"
            >
                @csrf
                <div class="space-y-2">
                    <label
                        for="2fa_code"
                        class="form-label"
                    >2FA Code :</label>
                    <input
                        id="2fa_code"
                        type="text"
                        name="2fa_code"
                        class="form-input"
                        value="{{ old('2fa_code') }}"
                        autofocus>
                </div>
                @error('2fa_code')
                <small class="text-red-500 text-sm">{{ $message }}</small>
                @enderror
        <div class="mt-2 md:mt-4 flex justify-end">
            <button
                type="submit"
                class="btn-accent"
            >
                Verify
            </button>
        </div>
    </form>
        </div>
    </div>
@endsection
