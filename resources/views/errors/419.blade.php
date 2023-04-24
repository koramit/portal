@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Page Expired'))
@section('link')
    <small class="mt-4 md:mt-12 text-sm text-teal-500 underline">
        @if(in_array(request()->route()->getName(), ['login.store', 'register.store']))
            <a href="{{ route(request()->route()->getName())  }}">Please reload page</a>
        @else
            <a href="{{ route('login')  }}">Please login again</a>
        @endif
    </small>
@endsection
