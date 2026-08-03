@extends('layouts.pegawai')

@section('title','Detail Slip Gaji')

@section('content')

<div class="max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-6">

        <div>

            <h2 class="text-3xl font-bold">
                Detail Slip Gaji
            </h2>

            <p class="text-gray-500 mt-1">
                Informasi lengkap slip gaji Anda.
            </p>

        </div>

        <div class="flex gap-3">

            <a href="{{ route('pegawai.slip') }}"
               class="px-5 py-3 rounded-xl border bg-white hover:bg-gray-100">

                Kembali

            </a>

            <a href="{{ route('pegawai.slip.pdf',$slip) }}"
               class="px-5 py-3 rounded-xl bg-green-700 hover:bg-green-800 text-white">

                Download PDF

            </a>

        </div>

    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <div class="bg-green-800 px-8 py-6 text-white">

            <h3 class="text-2xl font-bold">

                Slip Gaji - {{ $slip->bulan }} {{ $slip->tahun }}

            </h3>

            <p class="text-green-100">

                Terbit
                {{ \Carbon\Carbon::parse($slip->tanggal_terbit)->format('d F Y') }}

            </p>

        </div>

        <div class="p-8">

            <div class="grid grid-cols-2 gap-6 mb-8">

                <div class="border rounded-xl p-6">

                    <h4 class="font-bold text-lg mb-4">
                        Informasi Pegawai
                    </h4>

                    <div class="space-y-2">

                        <div class="flex justify-between">

                            <span>Nama</span>

                            <span class="font-semibold">
                                {{ $rincian['pegawai']['nama'] }}
                            </span>

                        </div>

                        <div class="flex justify-between">

                            <span>NIP</span>

                            <span class="font-semibold">
                                {{ $rincian['pegawai']['nip'] }}
                            </span>

                        </div>

                        <div class="flex justify-between">

                            <span>Golongan</span>

                            <span class="font-semibold">
                                {{ $rincian['pegawai']['golongan'] }}
                            </span>

                        </div>

                        <div class="flex justify-between">

                            <span>Jabatan</span>

                            <span class="font-semibold">
                                {{ $rincian['pegawai']['jabatan'] }}
                            </span>

                        </div>

                    </div>

                </div>

                <div class="border rounded-xl p-6">

                    <h4 class="font-bold text-lg mb-4">

                        Ringkasan

                    </h4>

                    <div class="space-y-2">

                        <div class="flex justify-between">

                            <span>Total Penghasilan</span>

                            <span class="font-semibold text-green-700">

                                Rp {{ number_format($rincian['total_pendapatan'],0,',','.') }}

                            </span>

                        </div>

                        <div class="flex justify-between">

                            <span>Total Potongan</span>

                            <span class="font-semibold text-red-600">

                                Rp {{ number_format($rincian['total_potongan'],0,',','.') }}

                            </span>

                        </div>

                        <div class="flex justify-between border-t pt-3">

                            <span class="font-bold">

                                Gaji Bersih

                            </span>

                            <span class="font-bold text-green-700 text-xl">

                                Rp {{ number_format($rincian['gaji_bersih'],0,',','.') }}

                            </span>

                        </div>

                    </div>

                </div>

            </div>

            <div class="grid grid-cols-2 gap-6">

                <div class="border rounded-xl">

                    <div class="bg-gray-100 px-5 py-4 font-bold">

                        Rincian Penghasilan

                    </div>

                    <div class="p-5">

                        @foreach($rincian['pendapatan'] as $nama=>$nilai)

                        <div class="flex justify-between py-2 border-b">

                            <span>{{ $nama }}</span>

                            <span>

                                Rp {{ number_format($nilai,0,',','.') }}

                            </span>

                        </div>

                        @endforeach

                    </div>

                </div>

                <div class="border rounded-xl">

                    <div class="bg-gray-100 px-5 py-4 font-bold">

                        Rincian Potongan

                    </div>

                    <div class="p-5">

                        @forelse($rincian['potongan'] as $nama=>$nilai)

                        <div class="flex justify-between py-2 border-b">

                            <span>{{ $nama }}</span>

                            <span class="text-red-600">

                                Rp {{ number_format($nilai,0,',','.') }}

                            </span>

                        </div>

                        @empty

                        <p class="text-gray-500">

                            Tidak ada potongan.

                        </p>

                        @endforelse

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection