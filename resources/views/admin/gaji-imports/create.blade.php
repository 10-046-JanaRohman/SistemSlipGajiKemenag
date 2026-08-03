@extends('layouts.admin')

@section('title','Import Gaji Excel')

@section('content')

@if ($errors->any())

<div class="mb-6 rounded-xl bg-red-100 border border-red-300 p-4">

    <h3 class="font-bold text-red-700 mb-2">

        Terjadi Kesalahan

    </h3>

    <ul class="list-disc ml-6 text-red-600">

        @foreach($errors->all() as $error)

            <li>{{ $error }}</li>

        @endforeach

    </ul>

</div>

@endif

<div class="max-w-4xl mx-auto">

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <div class="bg-green-800 px-8 py-5">
            <h2 class="text-2xl font-bold text-white">
                Import Gaji Bulanan
            </h2>
            <p class="text-green-100 mt-1">
                Upload 1 file Excel bulanan untuk pegawai dan slip gaji
            </p>
        </div>

        <form action="{{ route('gaji-imports.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-2 gap-6 p-8">

                <div>
                    <label class="block mb-2 font-semibold">Bulan</label>
                    <select name="bulan" class="w-full border rounded-xl p-3" required>
                        <option value="">-- Pilih Bulan --</option>
                        <option value="Januari">Januari</option>
                        <option value="Februari">Februari</option>
                        <option value="Maret">Maret</option>
                        <option value="April">April</option>
                        <option value="Mei">Mei</option>
                        <option value="Juni">Juni</option>
                        <option value="Juli">Juli</option>
                        <option value="Agustus">Agustus</option>
                        <option value="September">September</option>
                        <option value="Oktober">Oktober</option>
                        <option value="November">November</option>
                        <option value="Desember">Desember</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-2 font-semibold">Tahun</label>
                    <input type="number" name="tahun" value="{{ date('Y') }}" class="w-full border rounded-xl p-3" required>
                </div>

                <div class="col-span-2">
                    <label class="block mb-2 font-semibold">File Excel</label>
                    <input type="file" name="file_excel" accept=".xlsx,.xls,.csv" class="w-full border rounded-xl p-3" required>
                </div>

                <div class="col-span-2 bg-gray-50 border rounded-xl p-4 text-sm text-gray-600">
                    <p class="font-semibold mb-2">Catatan format file:</p>
                    <p>Pastikan baris pertama adalah header Excel seperti: <b>nip</b>, <b>nmpeg</b>, <b>gpokok</b>, <b>tjistri</b>, <b>tjanak</b>, <b>potpfkbul</b>, <b>bersih</b>, dan kolom lain dari file pembimbing.</p>
                </div>

            </div>

            <div class="bg-gray-100 px-8 py-5 flex justify-end gap-3">
                <a href="{{ route('slip-gaji.index') }}" class="px-6 py-3 rounded-xl border hover:bg-gray-200 transition">
                    Batal
                </a>

                <button type="submit" class="bg-green-700 hover:bg-green-800 transition text-white px-8 py-3 rounded-xl">
                    Import Excel
                </button>
            </div>

        </form>

    </div>

</div>

@endsection