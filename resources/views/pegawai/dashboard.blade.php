@extends('layouts.pegawai')

@section('title', 'Dashboard Pegawai')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-green-700 to-green-600 rounded-3xl text-white p-8 shadow-lg mb-8">

        <h1 class="text-3xl font-bold">
            Selamat Datang,
            {{ $pegawai->nama ?? auth()->user()->name }}
        </h1>

        <p class="mt-2 text-green-100">
            Portal Dashboard Slip Gaji Pegawai
            <br>
            Kantor Wilayah Kementerian Agama Provinsi Lampung
        </p>

    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

        <div class="bg-white rounded-2xl shadow p-6">

            <p class="text-gray-500 text-sm">
                Gaji Terakhir
            </p>

            <h2 class="text-2xl font-bold text-green-700 mt-2">
                Rp {{ number_format($gajiTerakhir,0,',','.') }}
            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow p-6">

            <p class="text-gray-500 text-sm">
                Jumlah Slip
            </p>

            <h2 class="text-2xl font-bold mt-2">

                {{ $totalSlip }}

            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow p-6">

            <p class="text-gray-500 text-sm">
                Slip Terakhir
            </p>

            <h2 class="text-xl font-bold mt-2">

                @if($slipTerakhir)

                    {{ $slipTerakhir->bulan }}
                    {{ $slipTerakhir->tahun }}

                @else

                    -

                @endif

            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow p-6">

            <p class="text-gray-500 text-sm">

                Status

            </p>

            <span
                class="inline-flex mt-3 px-4 py-2 rounded-full bg-green-100 text-green-700 font-semibold">

                {{ $statusSlip }}

            </span>

        </div>

    </div>

    {{-- Informasi Pegawai --}}
    <div class="bg-white rounded-2xl shadow">

        <div class="border-b px-8 py-5">

            <h2 class="text-xl font-bold">
                Informasi Pegawai
            </h2>

        </div>

        <div class="grid md:grid-cols-2 gap-6 p-8">

            <div>

                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Nama</span>
                    <span class="font-semibold">{{ $pegawai->nama }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">NIP</span>
                    <span class="font-semibold">{{ $pegawai->nip }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Golongan</span>
                    <span class="font-semibold">
                        {{ $pegawai->golongan ?? '-' }}
                    </span>
                </div>

                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Jabatan</span>
                    <span class="font-semibold">
                        {{ $pegawai->jabatan ?? '-' }}
                    </span>
                </div>

            </div>

            <div>

                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Unit Kerja</span>
                    <span class="font-semibold">
                        {{ $pegawai->unit_kerja ?? '-' }}
                    </span>
                </div>

                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Status Pegawai</span>
                    <span class="font-semibold">
                        {{ $pegawai->status_pegawai ?? '-' }}
                    </span>
                </div>

                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">No. HP</span>
                    <span class="font-semibold">
                        {{ $pegawai->no_hp ?? '-' }}
                    </span>
                </div>

                <div class="flex justify-between py-2">
                    <span class="text-gray-500">Tanggal Login</span>
                    <span class="font-semibold">

                        {{ now()->format('d F Y') }}

                    </span>
                </div>

            </div>

        </div>

    </div>

    {{-- Aksi Cepat --}}
    <div class="mt-8 flex gap-4">

        <a href="{{ route('pegawai.slip') }}"
           class="bg-green-700 hover:bg-green-800 text-white px-8 py-4 rounded-2xl font-semibold transition">

            Lihat Slip Saya

        </a>

    </div>

</div>

@endsection