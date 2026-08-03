@extends('layouts.admin')

@section('title', 'Riwayat Slip Gaji')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                Riwayat Slip Gaji
            </h1>
            <p class="text-gray-500 mt-1">
                Monitoring dan manajemen slip gaji seluruh pegawai.
            </p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <form method="GET" action="{{ route('admin.riwayat-slip') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            
            {{-- Search --}}
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                    Cari Pegawai
                </label>
                <input

                    type="text"

                    name="search"

                    id="search"

                    value="{{ request('search') }}"

                    placeholder="Nama atau NIP..."

                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"

                >
            </div>

            {{-- Filter Pegawai --}}
            <div>
                <label for="pegawai_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Pegawai
                </label>
                <select

                    name="pegawai_id"

                    id="pegawai_id"

                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"

                >
                    <option value="">Semua Pegawai</option>
                    @foreach($pegawaiList as $pegawai)
                        <option

                            value="{{ $pegawai->id }}"

                            {{ request('pegawai_id') == $pegawai->id ? 'selected' : '' }}

                        >
                            {{ $pegawai->nama }} ({{ $pegawai->nip }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Tahun --}}
            <div>
                <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">
                    Tahun
                </label>
                <select

                    name="tahun"

                    id="tahun"

                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"

                >
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $tahun)
                        <option

                            value="{{ $tahun }}"

                            {{ request('tahun') == $tahun ? 'selected' : '' }}

                        >
                            {{ $tahun }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Bulan --}}
            <div>
                <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">
                    Bulan
                </label>
                <select

                    name="bulan"

                    id="bulan"

                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"

                >
                    <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option

                                value="{{ $m }}"

                                {{ request('bulan') == $m ? 'selected' : '' }}

                            >
                                {{ \Carbon\Carbon::create()->month((int) $m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex items-end gap-2">
                <button

                    type="submit"

                    class="bg-green-700 text-white px-6 py-2 rounded-lg hover:bg-green-800 transition"

                >
                    Filter
                </button>
                <a

                    href="{{ route('admin.riwayat-slip') }}"

                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition"

                >
                    Reset
                </a>
            </div>

        </form>
    </div>

    {{-- Tabel Slip --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4">Pegawai</th>
                        <th class="text-left px-6 py-4">NIP</th>
                        <th class="text-center px-6 py-4">Periode</th>
                        <th class="text-right px-6 py-4">Gaji Bersih</th>
                        <th class="text-center px-6 py-4">Tanggal Terbit</th>
                        <th class="text-center px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($slips as $slip)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold">
                                    {{ $slip->pegawai->nama }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $slip->pegawai->jabatan ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                {{ $slip->pegawai->nip }}
                            </td>
                            <td class="text-center px-6 py-4">
                                {{ \Carbon\Carbon::create()->month((int) $slip->bulan)->translatedFormat('F') }}
                                {{ $slip->tahun }}
                            </td>
                            <td class="text-right px-6 py-4 font-semibold text-green-700">
                                Rp {{ number_format($slip->gaji_bersih, 0, ',', '.') }}
                            </td>
                            <td class="text-center px-6 py-4">
                                {{ \Carbon\Carbon::parse($slip->tanggal_terbit)->format('d M Y') }}
                            </td>
                            <td class="text-center px-6 py-4">
                                <div class="flex gap-2 justify-center">
                                    <a

                                        href="{{ route('slip-gaji.show', $slip->id) }}"

                                        class="bg-blue-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-700 transition"

                                        title="Lihat Detail"

                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a

                                        href="{{ route('slip-gaji.pdf', $slip->id) }}"

                                        class="bg-green-700 text-white px-3 py-1 rounded-lg text-sm hover:bg-green-800 transition"

                                        title="Download PDF"

                                        target="_blank"

                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">
                                Belum ada data slip gaji.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($slips->hasPages())
            <div class="p-6 border-t">
                {{ $slips->links() }}
            </div>
        @endif

    </div>

</div>

@endsection