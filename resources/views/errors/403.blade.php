@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')

<div class="min-h-screen flex items-center justify-center bg-gray-100">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8 text-center">

        <div class="mb-6">

            <svg class="w-24 h-24 mx-auto text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m9-9a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>

        </div>

        <h1 class="text-6xl font-bold text-gray-800 mb-4">403</h1>

        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Akses Ditolak</h2>

        <p class="text-gray-600 mb-8">
            Maaf, Anda tidak memiliki hak akses untuk melihat halaman ini.
        </p>

        <a href="{{ route('dashboard') }}"

           class="inline-block bg-green-700 text-white px-6 py-3 rounded-lg hover:bg-green-800 transition">

            Kembali ke Dashboard

        </a>

    </div>

</div>

@endsection