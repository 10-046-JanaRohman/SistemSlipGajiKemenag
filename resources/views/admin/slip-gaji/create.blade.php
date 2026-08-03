@extends('layouts.admin')

@section('title','Tambah Slip Gaji')

@section('content')

<div class="max-w-6xl mx-auto">

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <div class="bg-green-800 px-8 py-5">

            <h2 class="text-2xl font-bold text-white">
                Tambah Slip Gaji
            </h2>

            <p class="text-green-100 mt-1">
                Input data slip gaji pegawai Kementerian Agama Provinsi Lampung
            </p>

        </div>

        <form action="{{ route('slip-gaji.store') }}" method="POST" enctype="multipart/form-data">

            @csrf

            <div class="grid grid-cols-2 gap-6 p-8">

                {{-- Pegawai --}}
                <div>

                    <label class="block mb-2 font-semibold">
                        Pegawai
                    </label>

                    <select
                        name="pegawai_id"
                        class="w-full border rounded-xl p-3"
                        required>

                        <option value="">
                            -- Pilih Pegawai --
                        </option>

                        @foreach($pegawais as $pegawai)

                            <option value="{{ $pegawai->id }}">

                                {{ $pegawai->nama }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Bulan --}}
                <div>

                    <label class="block mb-2 font-semibold">
                        Bulan
                    </label>

                    <select
                        name="bulan"
                        class="w-full border rounded-xl p-3"
                        required>

                        <option>Januari</option>
                        <option>Februari</option>
                        <option>Maret</option>
                        <option>April</option>
                        <option>Mei</option>
                        <option>Juni</option>
                        <option>Juli</option>
                        <option>Agustus</option>
                        <option>September</option>
                        <option>Oktober</option>
                        <option>November</option>
                        <option>Desember</option>

                    </select>

                </div>

                {{-- Tahun --}}
                <div>

                    <label class="block mb-2 font-semibold">
                        Tahun
                    </label>

                    <input
                        type="number"
                        name="tahun"
                        value="{{ date('Y') }}"
                        class="w-full border rounded-xl p-3"
                        required>

                </div>

                {{-- Tanggal Terbit --}}
                <div>

                    <label class="block mb-2 font-semibold">
                        Tanggal Terbit
                    </label>

                    <input
                        type="date"
                        name="tanggal_terbit"
                        value="{{ date('Y-m-d') }}"
                        class="w-full border rounded-xl p-3"
                        required>

                </div>

                {{-- Gaji Pokok --}}
                <div>

                    <label class="block mb-2 font-semibold">
                        Gaji Pokok
                    </label>

                    <input
                        type="number"
                        id="gaji_pokok"
                        name="gaji_pokok"
                        class="w-full border rounded-xl p-3"
                        required>

                </div>

                {{-- Tunjangan --}}
                <div>

                    <label class="block mb-2 font-semibold">
                        Tunjangan
                    </label>

                    <input
                        type="number"
                        id="tunjangan"
                        name="tunjangan"
                        class="w-full border rounded-xl p-3"
                        required>

                </div>

                {{-- Potongan --}}
                <div>

                    <label class="block mb-2 font-semibold">
                        Potongan
                    </label>

                    <input
                        type="number"
                        id="potongan"
                        name="potongan"
                        class="w-full border rounded-xl p-3"
                        required>

                </div>

                {{-- Gaji Bersih --}}
                <div>

                    <label class="block mb-2 font-semibold">
                        Gaji Bersih
                    </label>

                    <input
                        type="number"
                        id="gaji_bersih"
                        name="gaji_bersih"
                        class="w-full border rounded-xl bg-gray-100 p-3"
                        readonly>

                </div>

                {{-- Upload PDF --}}
                <div class="col-span-2">

                    <label class="block mb-2 font-semibold">
                        Upload PDF Slip Gaji
                    </label>

                    <input
                        type="file"
                        name="file_pdf"
                        accept=".pdf"
                        class="w-full border rounded-xl p-3">

                </div>

            </div>

            <div class="bg-gray-100 px-8 py-5 flex justify-end gap-3">

                <a
                    href="{{ route('slip-gaji.index') }}"
                    class="px-6 py-3 border rounded-xl">

                    Batal

                </a>

                <button
                    type="submit"
                    class="bg-green-700 hover:bg-green-800 text-white px-8 py-3 rounded-xl">

                    Simpan Slip Gaji

                </button>

            </div>

        </form>

    </div>

</div>

@push('scripts')

    <script>

    const gajiPokok = document.getElementById('gaji_pokok');
    const tunjangan = document.getElementById('tunjangan');
    const potongan = document.getElementById('potongan');
    const gajiBersih = document.getElementById('gaji_bersih');

    function hitungGaji(){

        let pokok = parseFloat(gajiPokok.value) || 0;
        let tunj = parseFloat(tunjangan.value) || 0;
        let pot = parseFloat(potongan.value) || 0;

        gajiBersih.value = pokok + tunj - pot;

    }

    gajiPokok.addEventListener('input',hitungGaji);
    tunjangan.addEventListener('input',hitungGaji);
    potongan.addEventListener('input',hitungGaji);

    </script>

@endpush
@endsection