@extends('layout')

@section('content')
    <main class="p-4 md:p-12 xl:px-32">
        <nav class="flex justify-between items-center">
            <ul class="flex flex-row space-x-2">
                <li>
                    <a
                        class="text-sm md:text-base text-teal-500 hover:text-teal-700"
                        href="{{ route('service-request-forms') }}"
                    >Back</a>
                </li>
            </ul>
            <div>
                @include('partials.logout-form')
            </div>
        </nav>
        @error('error')
        <p class="my-8 md:my-12 text-xl text-red-500 font-bold">{{ $message  }}</p>
        @enderror
        @if(session('status'))
            <p class="my-8 md:my-12 text-xl text-teal-500 font-bold">{{ session('status') }}</p>
        @endif
        <form
            method="POST"
            action="{{ $action['url'] }}"
            class="my-4 md:my-12 p-4 md:p-8 bg-white rounded-lg shadow-md"
        >
            @csrf
            @method('PATCH')
            <div class="space-y-4">
                <div>
                    <label class="form-label">Requester :</label>
                    <p class="py-2 px-1 italic bg-white">{{ $form['requester']  }}</p>
                </div>
                <div>
                    <label class="form-label">Requests :</label>
                    <ul class="py-2 px-1 italic bg-white">
                        @foreach($form['requests'] as $request)
                            <li>{{ $request }}</li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <label class="form-label">Note :</label>
                    <p class="py-2 px-1 italic bg-white">{!! nl2br($form['note']) !!}</p>
                </div>
            </div>

            @if(strtolower($title) === 'response')
                <div class="mt-4 md:mt-12 flex space-x-2">
                    <label class="form-label">
                        <input
                            type="radio"
                            name="response"
                            value="approved"
                            class="form-radio"
                            @checked(old('response') === 'approved')
                        />
                        Approve
                    </label>
                    <label class="form-label">
                        <input
                            type="radio"
                            name="response"
                            value="disapproved"
                            class="form-radio"
                            @checked(old('response') === 'disapproved')
                        />
                        Disapprove
                    </label>
                </div>
                @error('response')
                <small class="text-red-500 text-sm">{{ $message }}</small>
                @enderror
            @endif

            <label class="mt-4 form-label">
                <textarea
                    class="form-input"
                    rows="4"
                    name="reply"
                    placeholder="reply..."
                >{{ old('reply') }}</textarea>
            </label>
            @error('reply')
            <small class="text-red-500 text-sm">{{ $message }}</small>
            @enderror

            <div class="mt-4 md:mt-12 flex justify-end">
                <button
                    type="submit"
                    class="btn-accent"
                >
                    {{ $action['label'] }}
                </button>
            </div>
        </form>
    </main>
@endsection
