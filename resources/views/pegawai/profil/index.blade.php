@extends('layouts.pegawai')

@section('title','Profil Pegawai')

@section('content')

<div class="max-w-6xl mx-auto">

    <div class="bg-white rounded-3xl shadow overflow-hidden">

        <div class="bg-green-700 px-8 py-8 text-white">

            <h2 class="text-3xl font-bold">
                Profil Pegawai
            </h2>

            <p class="text-green-100 mt-2">
                Informasi lengkap data pegawai.
            </p>

        </div>

        <div class="p-8">

            <div class="flex items-center gap-8 mb-10">

                <div
                    class="w-28 h-28 rounded-full bg-green-700 flex items-center justify-center text-white text-5xl font-bold">

                    {{ strtoupper(substr($pegawai->nama,0,1)) }}

                </div>

                <div>

                    <h3 class="text-2xl font-bold">

                        {{ $pegawai->nama }}

                    </h3>

                    <p class="text-gray-500">

                        {{ $pegawai->jabatan }}

                    </p>

                    <p class="text-green-700 font-semibold mt-2">

                        NIP :
                        {{ $pegawai->nip }}

                    </p>

                </div>

            </div>

            <div class="grid md:grid-cols-2 gap-8">

                <div class="space-y-4">

                    <div class="flex justify-between border-b pb-2">
                        <span>Nama Lengkap</span>
                        <strong>{{ $pegawai->nama }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>NIP</span>
                        <strong>{{ $pegawai->nip }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>NIP Lama</span>
                        <strong>{{ $pegawai->nip_lama ?? '-' }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>Jenis Kelamin</span>
                        <strong>{{ $pegawai->jenis_kelamin ?? '-' }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>Tempat Lahir</span>
                        <strong>{{ $pegawai->tempat_lahir ?? '-' }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>Tanggal Lahir</span>
                        <strong>
                            {{ optional($pegawai->tanggal_lahir)->format('d F Y') ?? '-' }}
                        </strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>Agama</span>
                        <strong>{{ $pegawai->agama ?? '-' }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>Status Kawin</span>
                        <strong>{{ $pegawai->status_kawin ?? '-' }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>Pendidikan</span>
                        <strong>{{ $pegawai->pendidikan ?? '-' }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>Alamat</span>
                        <strong>{{ $pegawai->alamat ?? '-' }}</strong>
                    </div>

                </div>

                <div class="space-y-4">

                    <div class="flex justify-between border-b pb-2">
                        <span>Status Pegawai</span>
                        <strong>{{ $pegawai->status_pegawai ?? '-' }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>Golongan</span>
                        <strong>{{ $pegawai->golongan ?? '-' }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>Jabatan</span>
                        <strong>{{ $pegawai->jabatan ?? '-' }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>Unit Kerja</span>
                        <strong>{{ $pegawai->unit_kerja ?? '-' }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>Email</span>
                        <strong>{{ $pegawai->user->email ?? '-' }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>No. HP</span>
                        <strong>{{ $pegawai->no_hp ?? '-' }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>NPWP</span>
                        <strong>{{ $pegawai->npwp ?? '-' }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>Bank</span>
                        <strong>{{ $pegawai->nama_bank ?? '-' }}</strong>
                    </div>

                    <div class="flex justify-between border-b pb-2">
                        <span>Nomor Rekening</span>
                        <strong>{{ $pegawai->rekening ?? '-' }}</strong>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection