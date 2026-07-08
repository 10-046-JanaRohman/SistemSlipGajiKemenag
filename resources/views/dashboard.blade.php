@extends('layouts.admin')

@section('content')

<div class="mb-8">

    <h2 class="text-3xl font-bold">

        Selamat Datang,

        {{ auth()->user()->name }}

    </h2>

    <p class="text-gray-500 mt-2">

        Sistem Dashboard Slip Gaji
        Kementerian Agama Provinsi Lampung

    </p>

</div>

<div class="grid grid-cols-4 gap-6">

    <div class="bg-white rounded-xl shadow p-6">

        <p class="text-gray-500">

            Total Pegawai

        </p>

        <h2 class="text-4xl font-bold mt-3">

            {{ $totalPegawai }}

        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-6">

        <p class="text-gray-500">

            Slip Gaji

        </p>

        <h2 class="text-4xl font-bold mt-3">

            {{ $totalSlip }}

        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-6">

        <p class="text-gray-500">

            Total Gaji

        </p>

        <h2 class="text-3xl font-bold mt-3">

            Rp {{ number_format($totalGaji,0,',','.') }}

        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-6">

        <p class="text-gray-500">

            Slip Terakhir

        </p>

        <h2 class="text-2xl font-bold mt-3">

            @if($slipTerakhir)

                {{ $slipTerakhir->bulan }}

                {{ $slipTerakhir->tahun }}

            @else

                Belum Ada

            @endif

        </h2>

    </div>

</div>

<div class="grid grid-cols-2 gap-6 mt-8">

    <div class="bg-white rounded-xl shadow p-6">

        <h3 class="font-bold text-xl mb-4">

            Informasi Terbaru

        </h3>

        <ul class="space-y-3">

            <li>• Dashboard berhasil dibuat.</li>

            <li>• Sistem Login aktif.</li>

            <li>• CRUD Pegawai aktif.</li>

            <li>• Database berhasil terkoneksi.</li>

        </ul>

    </div>

    <div class="bg-white rounded-xl shadow p-6">

        <h3 class="font-bold text-xl mb-4">

            Aktivitas Hari Ini

        </h3>

        <ul class="space-y-3">

            <li>✔ Login Admin</li>

            <li>✔ Membuka Dashboard</li>

            <li>✔ Sistem berjalan normal</li>

        </ul>

    </div>

</div>

@endsection