@extends('layouts.admin')

@section('title','Edit Pegawai')

@section('content')

<div class="max-w-5xl mx-auto">

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <div class="bg-green-800 px-8 py-5">

            <h2 class="text-2xl font-bold text-white">
                Edit Data Pegawai
            </h2>

            <p class="text-green-100 mt-1">
                Perbarui data pegawai Kementerian Agama Provinsi Lampung
            </p>

        </div>

        <form action="{{ route('pegawai.update', $pegawai) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-6 p-8">

                {{-- Nama --}}
                <div>

                    <label class="block mb-2 font-semibold">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        name="nama"
                        value="{{ old('nama', $pegawai->nama) }}"
                        class="w-full border rounded-xl p-3 focus:ring-2 focus:ring-green-600 @error('nama') border-red-500 @enderror"
                        required>

                    @error('nama')
                        <p class="text-red-600 text-sm mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- NIP --}}
                <div>

                    <label class="block mb-2 font-semibold">
                        NIP
                    </label>

                    <input
                        type="text"
                        value="{{ $pegawai->nip }}"
                        disabled
                        class="w-full border rounded-xl p-3 bg-gray-100 text-gray-600 cursor-not-allowed">

                    <p class="text-sm text-gray-500 mt-1">NIP tidak dapat diubah</p>

                </div>

                {{-- Email --}}
                <div>

                    <label class="block mb-2 font-semibold">
                        Email
                    </label>

                    <input
                        type="email"
                        value="{{ $pegawai->user->email ?? '' }}"
                        disabled
                        class="w-full border rounded-xl p-3 bg-gray-100 text-gray-600 cursor-not-allowed">

                    <p class="text-sm text-gray-500 mt-1">Email tidak dapat diubah</p>

                </div>

                {{-- Jabatan --}}
                <div>

                    <label class="block mb-2 font-semibold">
                        Jabatan
                    </label>

                    <input
                        type="text"
                        name="jabatan"
                        value="{{ old('jabatan', $pegawai->jabatan) }}"
                        class="w-full border rounded-xl p-3 @error('jabatan') border-red-500 @enderror"
                        required>

                    @error('jabatan')
                        <p class="text-red-600 text-sm mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Golongan --}}
                <div>

                    <label class="block mb-2 font-semibold">
                        Golongan
                    </label>

                    <input
                        type="text"
                        name="golongan"
                        value="{{ old('golongan', $pegawai->golongan) }}"
                        class="w-full border rounded-xl p-3 @error('golongan') border-red-500 @enderror"
                        required>

                    @error('golongan')
                        <p class="text-red-600 text-sm mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Unit Kerja --}}
                <div>

                    <label class="block mb-2 font-semibold">
                        Unit Kerja
                    </label>

                    <input
                        type="text"
                        name="unit_kerja"
                        value="{{ old('unit_kerja', $pegawai->unit_kerja) }}"
                        class="w-full border rounded-xl p-3 @error('unit_kerja') border-red-500 @enderror"
                        required>

                    @error('unit_kerja')
                        <p class="text-red-600 text-sm mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- No HP --}}
                <div class="col-span-2">

                    <label class="block mb-2 font-semibold">
                        Nomor HP
                    </label>

                    <input
                        type="text"
                        name="no_hp"
                        value="{{ old('no_hp', $pegawai->no_hp) }}"
                        class="w-full border rounded-xl p-3 @error('no_hp') border-red-500 @enderror">

                    @error('no_hp')
                        <p class="text-red-600 text-sm mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

            <div class="bg-gray-100 px-8 py-5 flex justify-end gap-3">

                <a
                    href="{{ route('pegawai.index') }}"
                    class="px-6 py-3 rounded-xl border hover:bg-gray-200 transition">

                    Batal

                </a>

                <button
                    type="submit"
                    class="bg-green-700 hover:bg-green-800 transition text-white px-8 py-3 rounded-xl">

                    Update Pegawai

                </button>

            </div>

        </form>

    </div>

</div>

@endsection