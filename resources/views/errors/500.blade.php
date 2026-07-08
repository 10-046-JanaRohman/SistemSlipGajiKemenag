@extends('layouts.app')

@section('title', 'Server Error')

@section('content')

<div class="min-h-screen flex items-center justify-center bg-gray-100">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8 text-center">

        <div class="mb-6">

            <svg class="w-24 h-24 mx-auto text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>

        </div>

        <h1 class="text-6xl font-bold text-gray-800 mb-4">500</h1>

        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Server Error</h2>

        <p class="text-gray-600 mb-8">
            Maaf, terjadi kesalahan pada server. Silakan coba lagi beberapa saat.
        </p>

        <a href="{{ route('dashboard') }}"

           class="inline-block bg-green-700 text-white px-6 py-3 rounded-lg hover:bg-green-800 transition">

            Kembali ke Dashboard

        </a>

    </div>

</div>

@endsection