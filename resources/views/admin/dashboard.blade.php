@extends('layouts.admin')

@section('content')

<div class="space-y-8">

    {{-- Header --}}
    <div>

        <h1 class="text-3xl font-bold text-gray-800">
            Dashboard Admin
        </h1>

        <p class="text-gray-500 mt-1">
            Selamat datang di Sistem Dashboard Slip Gaji Pegawai
            Kanwil Kementerian Agama Provinsi Lampung.
        </p>

    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <div class="bg-white rounded-2xl shadow p-6">

            <p class="text-gray-500">
                Total Pegawai
            </p>

            <h2 class="text-4xl font-bold text-green-700 mt-3">

                {{ number_format($totalPegawai) }}

            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow p-6">

            <p class="text-gray-500">
                Total Slip Keseluruhan
            </p>

            <h2 class="text-4xl font-bold text-blue-600 mt-3">

                {{ number_format($totalSlipKeseluruhan) }}

            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow p-6">

            <p class="text-gray-500">
                Total Gaji Keseluruhan
            </p>

            <h2 class="text-xl font-bold text-green-700 mt-3">

                Rp {{ number_format($totalGajiKeseluruhan,0,',','.') }}

            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow p-6">

            <p class="text-gray-500">
                Belum Terbit
            </p>

            <h2 class="text-4xl font-bold text-red-600 mt-3">

                {{ number_format($belumTerbit) }}

            </h2>

        </div>

    </div>

    {{-- Statistik Periode Terakhir --}}
    @if($importTerakhir)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white rounded-2xl shadow p-6">

            <p class="text-gray-500">
                Slip Periode {{ $importTerakhir->bulan }}/{{ $importTerakhir->tahun }}
            </p>

            <h2 class="text-4xl font-bold text-blue-600 mt-3">

                {{ number_format($totalSlip) }}

            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow p-6">

            <p class="text-gray-500">
                Total Gaji Periode {{ $importTerakhir->bulan }}/{{ $importTerakhir->tahun }}
            </p>

            <h2 class="text-xl font-bold text-green-700 mt-3">

                Rp {{ number_format($totalGaji,0,',','.') }}

            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow p-6">

            <p class="text-gray-500">
                Rata-rata Gaji Periode
            </p>

            <h2 class="text-xl font-bold text-purple-700 mt-3">

                Rp {{ number_format($rataRataGaji,0,',','.') }}

            </h2>

        </div>

    </div>
    @endif

    {{-- Informasi Import --}}
    <div class="grid lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-2xl shadow">

            <div class="border-b px-6 py-4">

                <h2 class="font-bold text-lg">
                    Import Terakhir
                </h2>

            </div>

            <div class="p-6">

                @if($importTerakhir)

                    <table class="w-full">

                        <tr class="border-b">
                            <td class="py-2 text-gray-500">Bulan</td>
                            <td class="font-semibold text-right">
                                {{ $importTerakhir->bulan }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="py-2 text-gray-500">Tahun</td>
                            <td class="font-semibold text-right">
                                {{ $importTerakhir->tahun }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="py-2 text-gray-500">Nama File</td>
                            <td class="font-semibold text-right">
                                {{ $importTerakhir->nama_file }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="py-2 text-gray-500">Berhasil</td>
                            <td class="font-semibold text-green-700 text-right">
                                {{ number_format($importTerakhir->berhasil) }}
                            </td>
                        </tr>

                        <tr>
                            <td class="py-2 text-gray-500">Gagal</td>
                            <td class="font-semibold text-red-600 text-right">
                                {{ number_format($importTerakhir->gagal) }}
                            </td>
                        </tr>

                    </table>

                @else

                    <div class="text-gray-500">

                        Belum ada proses import.

                    </div>

                @endif

            </div>

        </div>

        {{-- Slip Terbaru --}}

        <div class="bg-white rounded-2xl shadow">

            <div class="border-b px-6 py-4">

                <h2 class="font-bold text-lg">

                    5 Slip Terbaru

                </h2>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="text-left px-4 py-3">
                                Nama
                            </th>

                            <th class="text-center">
                                Periode
                            </th>

                            <th class="text-right px-4">
                                Gaji Bersih
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($slipTerbaru as $slip)

                            <tr class="border-t">

                                <td class="px-4 py-3">

                                    {{ $slip->pegawai->nama }}

                                </td>

                                <td class="text-center">

                                    {{ $slip->bulan }}/{{ $slip->tahun }}

                                </td>

                                <td class="text-right px-4 font-semibold text-green-700">

                                    Rp {{ number_format($slip->gaji_bersih,0,',','.') }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3"
                                    class="text-center py-8 text-gray-500">

                                    Belum ada data slip.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        {{-- Import Terbaru --}}

        <div class="bg-white rounded-2xl shadow">

            <div class="border-b px-6 py-4">

                <h2 class="font-bold text-lg">

                    5 Import Terbaru

                </h2>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="text-left px-4 py-3">
                                Periode
                            </th>

                            <th class="text-left px-4 py-3">
                                Nama File
                            </th>

                            <th class="text-center px-4 py-3">
                                Status
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($importTerbaru as $import)

                            <tr class="border-t">

                                <td class="px-4 py-3">

                                    {{ $import->bulan }}/{{ $import->tahun }}

                                </td>

                                <td class="px-4 py-3">

                                    {{ $import->nama_file }}

                                </td>

                                <td class="text-center px-4 py-3">

                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">

                                        Berhasil: {{ number_format($import->berhasil) }}

                                    </span>

                                    @if($import->gagal > 0)

                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm ml-1">

                                            Gagal: {{ number_format($import->gagal) }}

                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3"
                                    class="text-center py-8 text-gray-500">

                                    Belum ada data import.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection