@extends('layouts.app')

@section('title', 'Server Error')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center text-center space-y-4">
    <h1 class="text-5xl font-bold text-red-600">500</h1>
    <h2 class="text-2xl font-semibold">Oops! Something went wrong.</h2>
    <p class="text-gray-500">{{ $message ?? 'An unexpected error occurred while loading the page.' }}</p>

    <a href="{{ url('/') }}" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        Go Back Home
    </a>
</div>
@endsection
