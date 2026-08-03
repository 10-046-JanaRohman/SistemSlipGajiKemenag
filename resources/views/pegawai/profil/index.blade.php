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

                        {{ $pegawai->jabatan ?? 'Belum ada jabatan' }}

                    </p>

                    @if($pegawai->nip)
                    <p class="text-green-700 font-semibold mt-2">

                        NIP :
                        {{ $pegawai->nip }}

                    </p>
                    @endif

                </div>

            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="space-y-4">

                    @if($pegawai->nama)
                    <div class="flex justify-between border-b pb-2">
                        <span>Nama Lengkap</span>
                        <strong>{{ $pegawai->nama }}</strong>
                    </div>
                    @endif

                    @if($pegawai->nip)
                    <div class="flex justify-between border-b pb-2">
                        <span>NIP</span>
                        <strong>{{ $pegawai->nip }}</strong>
                    </div>
                    @endif

                    @if($pegawai->nip_lama)
                    <div class="flex justify-between border-b pb-2">
                        <span>NIP Lama</span>
                        <strong>{{ $pegawai->nip_lama }}</strong>
                    </div>
                    @endif

                    @if($pegawai->jenis_kelamin)
                    <div class="flex justify-between border-b pb-2">
                        <span>Jenis Kelamin</span>
                        <strong>{{ $pegawai->jenis_kelamin }}</strong>
                    </div>
                    @endif

                    @if($pegawai->tempat_lahir)
                    <div class="flex justify-between border-b pb-2">
                        <span>Tempat Lahir</span>
                        <strong>{{ $pegawai->tempat_lahir }}</strong>
                    </div>
                    @endif

                    @if($pegawai->tanggal_lahir)
                    <div class="flex justify-between border-b pb-2">
                        <span>Tanggal Lahir</span>
                        <strong>{{ optional($pegawai->tanggal_lahir)->format('d F Y') }}</strong>
                    </div>
                    @endif

                    @if($pegawai->agama)
                    <div class="flex justify-between border-b pb-2">
                        <span>Agama</span>
                        <strong>{{ $pegawai->agama }}</strong>
                    </div>
                    @endif

                    @if($pegawai->status_kawin)
                    <div class="flex justify-between border-b pb-2">
                        <span>Status Kawin</span>
                        <strong>{{ $pegawai->status_kawin }}</strong>
                    </div>
                    @endif

                    @if($pegawai->pendidikan)
                    <div class="flex justify-between border-b pb-2">
                        <span>Pendidikan</span>
                        <strong>{{ $pegawai->pendidikan }}</strong>
                    </div>
                    @endif

                    @if($pegawai->alamat)
                    <div class="flex justify-between border-b pb-2">
                        <span>Alamat</span>
                        <strong>{{ $pegawai->alamat }}</strong>
                    </div>
                    @endif

                    @if($pegawai->no_hp)
                    <div class="flex justify-between border-b pb-2">
                        <span>No. HP</span>
                        <strong>{{ $pegawai->no_hp }}</strong>
                    </div>
                    @endif

                </div>

                <div class="space-y-4">

                    @if($pegawai->status_pegawai)
                    <div class="flex justify-between border-b pb-2">
                        <span>Status Pegawai</span>
                        <strong>{{ $pegawai->status_pegawai }}</strong>
                    </div>
                    @endif

                    @if($pegawai->golongan)
                    <div class="flex justify-between border-b pb-2">
                        <span>Golongan</span>
                        <strong>{{ $pegawai->golongan }}</strong>
                    </div>
                    @endif

                    @if($pegawai->jabatan)
                    <div class="flex justify-between border-b pb-2">
                        <span>Jabatan</span>
                        <strong>{{ $pegawai->jabatan }}</strong>
                    </div>
                    @endif

                    @if($pegawai->unit_kerja)
                    <div class="flex justify-between border-b pb-2">
                        <span>Unit Kerja</span>
                        <strong>{{ $pegawai->unit_kerja }}</strong>
                    </div>
                    @endif

                    @if($pegawai->user && $pegawai->user->email)
                    <div class="flex justify-between border-b pb-2">
                        <span>Email</span>
                        <strong>{{ $pegawai->user->email }}</strong>
                    </div>
                    @endif

                    @if($pegawai->npwp)
                    <div class="flex justify-between border-b pb-2">
                        <span>NPWP</span>
                        <strong>{{ $pegawai->npwp }}</strong>
                    </div>
                    @endif

                    @if($pegawai->nama_bank)
                    <div class="flex justify-between border-b pb-2">
                        <span>Bank</span>
                        <strong>{{ $pegawai->nama_bank }}</strong>
                    </div>
                    @endif

                    @if($pegawai->rekening)
                    <div class="flex justify-between border-b pb-2">
                        <span>Nomor Rekening</span>
                        <strong>{{ $pegawai->rekening }}</strong>
                    </div>
                    @endif

                    @if($pegawai->status_kerja)
                    <div class="flex justify-between border-b pb-2">
                        <span>Status Kerja</span>
                        <strong>{{ $pegawai->status_kerja }}</strong>
                    </div>
                    @endif

                    @if($pegawai->jenjang_pendidikan)
                    <div class="flex justify-between border-b pb-2">
                        <span>Jenjang Pendidikan</span>
                        <strong>{{ $pegawai->jenjang_pendidikan }}</strong>
                    </div>
                    @endif

                </div>
            </div>

            @php
                $satkerFields = collect([
                    'Keterangan Satuan Kerja' => $pegawai->keterangan_satuan_kerja,
                    'Satker 1' => $pegawai->satker_1,
                    'Satker 2' => $pegawai->satker_2,
                    'Satker 3' => $pegawai->satker_3,
                    'Satker 4' => $pegawai->satker_4,
                    'Satker 5' => $pegawai->satker_5,
                    'Grup Satuan Kerja' => $pegawai->grup_satuan_kerja,
                ])->filter();
            @endphp

            @if($satkerFields->isNotEmpty())
            <div class="mt-8 pt-6 border-t">
                <h3 class="font-bold text-lg mb-4">Informasi Satuan Kerja</h3>
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        @foreach($satkerFields as $label => $value)
                        <div class="flex justify-between border-b pb-2">
                            <span>{{ $label }}</span>
                            <strong>{{ $value }}</strong>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>

    </div>

</div>

@endsection