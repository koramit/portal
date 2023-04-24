@extends('layout')

@section('content')
    <div class="flex flex-col w-full min-h-screen justify-center items-center bg-slate-50">
        <div class="-mt-8 mx-auto w-96 md:w-1/3 p-8 md:p-12">
            <div class="mx-auto px-4 py-2 w-48 rounded-3xl bg-gradient-to-br from-teal-500 to-sky-500">
                <h1 class="text-2xl md:text-3xl font-bold text-center text-white">PORTAL :</h1>
                <p class="text-sm text-center italic text-teal-200">: the APIs gateway</p>
            </div>
            @yield('form')
        </div>
    </div>
@endsection
