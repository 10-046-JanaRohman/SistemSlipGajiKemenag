@extends('layouts.pegawai')

@section('title','Riwayat Slip')

@section('content')

<div class="max-w-6xl mx-auto">

    <div class="bg-white rounded-2xl shadow">

        <div class="p-6 border-b">

            <h2 class="text-3xl font-bold">

                Riwayat Slip Gaji

            </h2>

            <p class="text-gray-500 mt-2">

                Seluruh slip gaji yang pernah diterbitkan.

            </p>

        </div>

        {{-- Filter --}}
        <div class="p-6 border-b bg-gray-50">
            <form method="GET" action="{{ route('pegawai.riwayat') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <select name="tahun" id="tahun" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunList as $tahun)
                            <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <select name="bulan" id="bulan" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month((int) $m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-green-700 text-white px-6 py-2 rounded-lg hover:bg-green-800 transition">
                        Filter
                    </button>
                    <a href="{{ route('pegawai.riwayat') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <table class="w-full">

            <thead class="bg-green-700 text-white">

                <tr>

                    <th class="text-left px-6 py-4">Periode</th>

                    <th class="text-left px-6 py-4">Tanggal</th>

                    <th class="text-left px-6 py-4">Status</th>

                    <th class="text-center px-6 py-4">Aksi</th>

                </tr>

            </thead>

            <tbody>

            @forelse($slips as $slip)

                <tr class="border-b">

                    <td class="px-6 py-4">

                        {{ \Carbon\Carbon::create()->month((int) $slip->bulan)->translatedFormat('F') }}
                        {{ $slip->tahun }}

                    </td>

                    <td class="px-6 py-4">

                        {{ \Carbon\Carbon::parse($slip->tanggal_terbit)->format('d M Y') }}

                    </td>

                    <td class="px-6 py-4">

                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">

                            Terbit

                        </span>

                    </td>

                    <td class="px-6 py-4 text-center space-x-2">

                        <a
                            href="{{ route('pegawai.slip.show',$slip) }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg">

                            Lihat

                        </a>

                        <a
                            href="{{ route('pegawai.slip.pdf',$slip) }}"
                            class="bg-green-700 text-white px-4 py-2 rounded-lg">

                            PDF

                        </a>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="4" class="text-center py-8 text-gray-500">

                        Belum ada riwayat slip.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

        <div class="p-6">

            {{ $slips->links() }}

        </div>

    </div>

</div>

@endsection