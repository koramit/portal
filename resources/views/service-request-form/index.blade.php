@extends('layout')

@section('content')
    <main class="p-4 md:p-12 xl:px-32">
        <nav class="flex justify-between items-center">
            <ul class="flex flex-row space-x-2">
                <li>
                    <a
                        class="text-sm md:text-base text-teal-500 hover:text-teal-700"
                        href="{{ route('dashboard') }}"
                    >Dashboard</a>
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
            action="{{ route('service-request-forms.store') }}"
            class="my-4 md:my-12 p-4 md:p-8 bg-white rounded-lg shadow-md"
        >
            <h1 class="text-xl text-teal-500 font-bold">Request Form</h1>
            <div class="my-4 md:my-8 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8">
                @csrf
                @foreach($services as $service)
                <div>
                    <label>
                        <input
                            class="mr-1 md:mr-2"
                            type="checkbox"
                            name="{{ $service['name'] }}"
                            @checked(old($service['name']))
                        />
                        {{ $service['label'] }}
                    </label>
                </div>
                @endforeach
            </div>

            <label class="form-label">Note
                <textarea
                    class="form-input"
                    rows="4"
                    name="note"
                    placeholder="Tell us more about your request..."
                >{{ old('note') }}</textarea>
            </label>
            @error('note')
            <small class="text-red-500 text-sm">{{ $message }}</small>
            @enderror

            <div
                class="mt-4 md:mt-12 md:flex md:justify-end"
            >
                <button
                    class="btn-accent w-full md:w-1/2"
                    type="submit"
                >Submit</button>
            </div>
        </form>

        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Submitted at
                    </th>
                    @if(Auth::user()->can('approve_request_form'))
                        <th scope="col" class="px-6 py-3">
                            Requester
                        </th>
                    @endif
                    <th scope="col" class="px-6 py-3">
                        Request
                    </th>
                    <th scope="col" colspan="2" class="px-6 py-3">
                        Status
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($forms as $form)
                    <tr class="bg-white border-b">
                        <th scope="row" class="px-6 py-4 font-medium text-slate-900 whitespace-nowrap">
                            {{ $form['submitted_at'] }}
                        </th>
                        @if(Auth::user()->can('approve_request_form'))
                            <th scope="col" class="px-6 py-3">
                                {{ $form['requester'] }}
                            </th>
                        @endif
                        <td class="px-6 py-4">
                            <ul>
                                @foreach($form['requests'] as $request)
                                    <li>
                                        <span
                                            class="inline-block rounded-full px-2 py-1 text-xs font-semibold mr-2 mb-2 bg-slate-100 text-slate-500"
                                        >{{ $request }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-6 py-4">
                            {{ $form['status'] }}
                        </td>
                        <td class="px-6 py-4">
                            <ul>
                                @foreach($form['actions'] as $action)
                                    <li>
                                        @if($action['type'] === 'form')
                                            <form
                                                method="POST"
                                                action="{{ $action['url'] }}"
                                            >
                                                @csrf
                                                @method($action['method'])
                                                <button
                                                    @class([
                                                    'cursor-pointer',
                                                    'text-amber-500 hover:text-amber-700' => $action['label'] === 'Cancel',
                                                    'text-red-500 hover:text-red-700' => $action['label'] === 'Revoke',
                                                ])
                                                    type="submit"
                                                >{{ $action['label'] }}</button>
                                            </form>
                                        @elseif($action['type'] === 'link')
                                            <a
                                                class="text-teal-500 hover:text-teal-700"
                                                href="{{ $action['url'] }}"
                                            >{{ $action['label'] }}</a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </main>
@endsection
