@extends('layout')

@section('content')
    <main class="p-4 md:p-12 xl:px-32">
        <nav class="flex justify-between items-center">
            <ul class="flex flex-row space-x-4">
                <li>
                    <a
                        class="text-sm md:text-base text-teal-500 hover:text-teal-700"
                        href="{{ route('dashboard') }}"
                    >Dashboard</a>
                </li>
                <li>
                    <a
                        class="text-sm md:text-base text-teal-500 hover:text-teal-700"
                        href="{{ route('service-request-forms') }}"
                    >Request Service</a>
                </li>
            </ul>
            <div>
                @include('partials.logout-form')
            </div>
        </nav>
        <form
            method="POST"
            action="{{ route('setup-notification.store') }}"
            class="my-4 md:my-12 p-4 md:p-8 bg-white rounded-lg shadow-md"
        >
            @csrf
            <input type="hidden" name="provider" value="slack">
            <h1 class="text-xl text-teal-500 font-bold">Slack notification</h1>
            <div class="mt-2 md:mt-4 space-y-2">
                <label
                    class="form-label"
                    for="webhook_url"
                >Webhook URL :</label>
                <input
                    class="form-input"
                    name="webhook_url"
                    id="webhook_url"
                    value="{{ old('webhook_url') }}"
                    autofocus
                />
                @error('webhook_url')
                <small class="text-red-500 text-sm">{{ $message }}</small>
                @enderror
            </div>
            <div class="mt-4 md:mt-12 md:flex md:justify-end">
                <button
                    class="btn-accent w-full md:w-1/2"
                    type="submit"
                >Update</button>
            </div>
        </form>
    </main>
@endsection
