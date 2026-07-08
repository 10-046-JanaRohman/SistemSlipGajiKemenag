@extends('layouts.admin')

@section('title','Detail Slip Gaji')

@section('content')

<div class="max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-6">

        <div>
            <h2 class="text-3xl font-bold">Detail Slip Gaji</h2>
            <p class="text-gray-500 mt-1">
                Informasi lengkap slip gaji pegawai
            </p>
        </div>

        <div class="flex gap-3">

            <a href="{{ route('slip-gaji.index') }}"
               class="px-5 py-3 rounded-xl border bg-white hover:bg-gray-100 transition">
                Kembali
            </a>

           <a href="{{ route('slip-gaji.pdf', $slip_gaji) }}"
            class="px-5 py-3 rounded-xl bg-green-700 hover:bg-green-800 text-white transition">
                Cetak / Simpan PDF
            </a>

        </div>

    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <div class="bg-green-800 px-8 py-6 text-white">

            <h3 class="text-2xl font-bold">
                Slip Gaji - {{ $slip_gaji->bulan }} {{ $slip_gaji->tahun }}
            </h3>

            <p class="text-green-100 mt-1">
                Terbit:
                {{ \Carbon\Carbon::parse($slip_gaji->tanggal_terbit)->format('d F Y') }}
            </p>

        </div>

        <div class="p-8">

            <div class="grid grid-cols-2 gap-6 mb-8">

                <div class="border rounded-2xl p-6 bg-gray-50">
                    <h4 class="font-bold text-lg mb-4">Informasi Pegawai</h4>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Nama</span>
                            <span class="font-semibold">{{ $slip_gaji->pegawai->nama }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">NIP</span>
                            <span class="font-semibold">{{ $slip_gaji->pegawai->nip }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Jabatan</span>
                            <span class="font-semibold">{{ $slip_gaji->pegawai->jabatan }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Unit Kerja</span>
                            <span class="font-semibold">{{ $slip_gaji->pegawai->unit_kerja }}</span>
                        </div>
                    </div>
                </div>

                <div class="border rounded-2xl p-6 bg-gray-50">
                    <h4 class="font-bold text-lg mb-4">Ringkasan Slip</h4>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Bulan</span>
                            <span class="font-semibold">{{ $slip_gaji->bulan }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Tahun</span>
                            <span class="font-semibold">{{ $slip_gaji->tahun }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Tanggal Terbit</span>
                            <span class="font-semibold">
                                {{ \Carbon\Carbon::parse($slip_gaji->tanggal_terbit)->format('d/m/Y') }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Gaji Bersih</span>
                            <span class="font-bold text-green-700 text-lg">
                                Rp {{ number_format($slip_gaji->gaji_bersih, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-2 gap-6">

                <div class="border rounded-2xl overflow-hidden">
                    <div class="bg-gray-100 px-6 py-4 font-bold">
                        Penghasilan
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="flex justify-between">
                            <span>Gaji Pokok</span>
                            <span class="font-semibold">
                                Rp {{ number_format($slip_gaji->gaji_pokok, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span>Tunjangan</span>
                            <span class="font-semibold">
                                Rp {{ number_format($slip_gaji->tunjangan, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between border-t pt-4 font-bold">
                            <span>Total Penghasilan</span>
                            <span class="text-green-700">
                                Rp {{ number_format($slip_gaji->gaji_pokok + $slip_gaji->tunjangan, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border rounded-2xl overflow-hidden">
                    <div class="bg-gray-100 px-6 py-4 font-bold">
                        Potongan
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="flex justify-between">
                            <span>Potongan</span>
                            <span class="font-semibold">
                                Rp {{ number_format($slip_gaji->potongan, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between border-t pt-4 font-bold">
                            <span>Total Potongan</span>
                            <span class="text-red-600">
                                Rp {{ number_format($slip_gaji->potongan, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between border-t pt-4 font-bold">
                            <span>Gaji Bersih</span>
                            <span class="text-green-700 text-lg">
                                Rp {{ number_format($slip_gaji->gaji_bersih, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection