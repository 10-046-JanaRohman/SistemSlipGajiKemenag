@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')

<div class="min-h-screen flex items-center justify-center bg-gray-100">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8 text-center">

        <div class="mb-6">

            <svg class="w-24 h-24 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>

        </div>

        <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>

        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Halaman Tidak Ditemukan</h2>

        <p class="text-gray-600 mb-8">
            Maaf, halaman yang Anda cari tidak ditemukan atau telah dipindahkan.
        </p>

        <a href="{{ route('dashboard') }}"

           class="inline-block bg-green-700 text-white px-6 py-3 rounded-lg hover:bg-green-800 transition">

            Kembali ke Dashboard

        </a>

    </div>

</div>

@endsection