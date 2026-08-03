@extends('layouts.pegawai')

@section('title', 'Ganti Password')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-2xl shadow">

        <div class="p-6 border-b">

            <h2 class="text-3xl font-bold">

                Ganti Password

            </h2>

            <p class="text-gray-500 mt-2">

                Ubah password akun Anda secara berkala untuk keamanan.

            </p>

        </div>

        @if(session('success'))

            <div class="m-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">

                {{ session('success') }}

            </div>

        @endif

        <div class="p-6">

            <form method="POST" action="{{ route('pegawai.ganti-password.update') }}">

                @csrf
                @method('PATCH')

                {{-- Password Lama --}}
                <div class="mb-6">

                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">

                        Password Lama

                    </label>

                    <input

                        type="password"

                        name="current_password"

                        id="current_password"

                        required

                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('current_password') border-red-500 @enderror"

                    >

                    @error('current_password')

                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>

                    @enderror

                </div>

                {{-- Password Baru --}}
                <div class="mb-6">

                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">

                        Password Baru

                    </label>

                    <input

                        type="password"

                        name="password"

                        id="password"

                        required

                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('password') border-red-500 @enderror"

                    >

                    @error('password')

                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>

                    @enderror

                    <p class="mt-1 text-sm text-gray-500">

                        Minimal 8 karakter, kombinasi huruf dan angka.

                    </p>

                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-6">

                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">

                        Konfirmasi Password Baru

                    </label>

                    <input

                        type="password"

                        name="password_confirmation"

                        id="password_confirmation"

                        required

                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"

                    >

                </div>

                {{-- Actions --}}
                <div class="flex gap-3">

                    <button

                        type="submit"

                        class="bg-green-700 text-white px-6 py-3 rounded-lg hover:bg-green-800 transition"

                    >

                        Ubah Password

                    </button>

                    <a

                        href="{{ route('pegawai.dashboard') }}"

                        class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition"

                    >

                        Batal

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection