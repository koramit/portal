@extends('layout')

@section('content')
    <main class="p-4 md:p-12 xl:px-32">
        <nav class="flex justify-between items-center">
            <ul class="flex flex-row space-x-4">
                <li>
                    <a
                        class="text-sm md:text-base text-teal-500 hover:text-teal-700"
                        href="{{ route('setup-notification') }}"
                    >Setup Notification</a>
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
        @error('error')
            <p class="my-8 md:my-12 text-xl text-red-500 font-bold">{{ $message  }}</p>
        @enderror
        @if(session('status'))
            <p class="my-8 md:my-12 text-xl text-teal-500 font-bold">{{ session('status') }}</p>
        @endif
        @if(session('token'))
            <div class="mt-2 md:mt-4 space-y-2">
                <label
                    class="form-label"
                    for="token"
                >Token :</label>
                <input
                    class="form-input"
                    name="token"
                    id="token"
                    value="{{ session('token') }}"
                    readonly />
            </div>
        @endif
        @can('create_app')
        <form
            method="POST"
            action="{{ route('app-tokens.store') }}"
            class="my-4 md:my-12 p-4 md:p-8 bg-white rounded-lg shadow-md"
        >
            <h1 class="text-xl text-teal-500 font-bold">Create App</h1>
            @csrf
            <div class="mt-2 md:mt-4 space-y-2">
                <label
                    class="form-label"
                    for="name"
                >Name :</label>
                <input
                    class="form-input"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    autofocus
                />
                @error('name')
                <small class="text-red-500 text-sm">{{ $message }}</small>
                @enderror
            </div>
            <label class="mt-2 md:mt-4 form-label">Abilities :</label>
            <div class="mt-2 md:mt-4 grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-4">
                @foreach((new \App\Services\RoleUserService())->availableServices(\Illuminate\Support\Facades\Auth::user()) as $ability)
                    <div>
                        <label>
                            <input
                                class="mr-1 md:mr-2"
                                type="checkbox"
                                name="{{ $ability['name'] }}"
                                @checked(old($ability['name']))
                            />
                            {{ $ability['label'] }}
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 md:mt-12 md:flex md:justify-end">
                <button
                    class="btn-accent w-full md:w-1/2"
                    type="submit"
                >Create</button>
            </div>
        </form>
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Name
                    </th>
                    @if(Auth::user()->can('revoke_any_tokens'))
                        <th scope="col" class="px-6 py-3">
                            Developer
                        </th>
                    @endif
                    <th scope="col" class="px-6 py-3">
                        Abilities
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Calls
                    </th>
                    <th scope="col" colspan="2" class="px-6 py-3">
                        Last used
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse($appTokens as $token)
                    <tr class="bg-white border-b">
                        <td class="px-6 py-3">
                            {{ $token->name }}
                        </td>
                        @if(Auth::user()->can('revoke_any_tokens'))
                            <td class="px-6 py-3">
                                {{ $token->tokenable->full_name }}
                            </td>
                        @endif
                        <td class="px-6 py-3">
                            @foreach($token->abilities as $ability)
                                <span class="inline-block bg-slate-100 text-slate-500 rounded-full px-2 py-1 text-xs font-semibold mr-2 mb-2">
                                    {{ $tokenAbilityLabelMapping[$ability] }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-3">
                            <span @class([
                                'inline-block rounded-full px-2 py-1 text-xs font-semibold mr-2 mb-2',
                                'bg-teal-100 text-teal-500' => $token->status === 'active',
                                'bg-slate-100 text-slate-500' => $token->status === 'expired',
                                'bg-amber-100 text-amber-500' => $token->status === 'revoked',
                            ])>{{ $token->status }}</span>
                        </td>
                        <td class="px-6 py-3">
                            {{ $token->service_access_logs_count }}
                        </td>
                        <td class="px-6 py-3">
                            {{ $token->last_used_at }}
                        </td>
                        <td class="px-6 py-3">
                            @can('destroy', $token)
                            <form
                                method="POST"
                                action="{{ route('app-tokens.destroy', $token->hashed_key) }}"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    class="text-sm text-red-500 hover:text-red-700"
                                    type="submit"
                                >Revoke</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-center">No data</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $appTokens->links() }}
        </div>
        @endcan
    </main>
@endsection
