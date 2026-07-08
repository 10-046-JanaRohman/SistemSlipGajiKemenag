@extends('layouts.admin')

@section('content')
@if(session('success'))

<div class="bg-green-100
            border
            border-green-400
            text-green-700
            px-5
            py-4
            rounded-lg
            mb-5">

    {{ session('success') }}

</div>

@endif

<div class="flex justify-between items-center mb-6">

    <div>

        <h2 class="text-3xl font-bold">

            Data Pegawai

        </h2>

        <p class="text-gray-500 mt-1">

            Kelola data pegawai Kementerian Agama Provinsi Lampung

        </p>

    </div>

    <a href="{{ route('pegawai.create') }}"
       class="bg-green-700 hover:bg-green-800 text-white px-5 py-3 rounded-lg">

        + Tambah Pegawai

    </a>

</div>

<div class="bg-white rounded-xl shadow">

    <div class="p-6 border-b">

        <form method="GET">

            <input
                type="text"
                name="search"
                placeholder="Cari nama atau NIP..."
                class="w-full border rounded-lg px-4 py-2">

        </form>

    </div>

    <table class="w-full">

        <thead class="bg-gray-100">

            <tr>

                <th class="p-4">No</th>

                <th>Nama</th>

                <th>NIP</th>

                <th>Jabatan</th>

                <th>Golongan</th>

                <th>Unit Kerja</th>

                <th>No HP</th>

                <th>Aksi</th>

            </tr>

        </thead>

        <tbody>

        @forelse($pegawais as $pegawai)

            <tr class="border-t">

                <td class="p-4">

                    {{ $loop->iteration }}

                </td>

                <td>

                    {{ $pegawai->nama }}

                </td>

                <td>

                    {{ $pegawai->nip }}

                </td>

                <td>

                    {{ $pegawai->jabatan }}

                </td>

                <td>

                    {{ $pegawai->golongan }}

                </td>

                <td>

                    {{ $pegawai->unit_kerja }}

                </td>

                <td>

                    {{ $pegawai->no_hp }}

                </td>

                <td>

                    <div class="flex gap-2">

                        <a href="{{ route('pegawai.edit',$pegawai) }}"
                           class="bg-blue-600 text-white px-3 py-1 rounded">

                            Edit

                        </a>

                        <form
                            action="{{ route('pegawai.destroy',$pegawai) }}"
                            method="POST">

                            @csrf
                            @method('DELETE')

                            <button
                                class="bg-red-600 text-white px-3 py-1 rounded">

                                Hapus

                            </button>

                        </form>

                    </div>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="8"
                    class="text-center p-10 text-gray-500">

                    Belum ada data pegawai.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>

@endsection