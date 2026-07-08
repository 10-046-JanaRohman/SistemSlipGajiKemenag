@extends('layouts.admin')

@section('title', 'Slip Gaji')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-3xl font-bold">Data Slip Gaji</h2>
        <p class="text-gray-500 mt-1">Kelola seluruh slip gaji pegawai</p>
    </div>

    <a href="{{ route('slip-gaji.create') }}"
       class="bg-green-700 hover:bg-green-800 text-white px-5 py-3 rounded-lg">
        + Tambah Slip Gaji
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-4 text-left">No</th>
                <th class="p-4 text-left">Pegawai</th>
                <th class="p-4 text-left">Bulan</th>
                <th class="p-4 text-left">Tahun</th>
                <th class="p-4 text-left">Gaji Bersih</th>
                <th class="p-4 text-left">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse($slips as $slip)
                <tr class="border-t">
                    <td class="p-4">
                        {{ $slips->firstItem() + $loop->index }}
                    </td>

                    <td class="p-4">
                        {{ $slip->pegawai->nama ?? '-' }}
                    </td>

                    <td class="p-4">
                        {{ $slip->bulan }}
                    </td>

                    <td class="p-4">
                        {{ $slip->tahun }}
                    </td>

                    <td class="p-4">
                        Rp {{ number_format($slip->gaji_bersih ?? 0, 0, ',', '.') }}
                    </td>

                    <td class="p-4">
                        <a href="{{ route('slip-gaji.show', $slip) }}"
                           class="bg-blue-600 text-white px-3 py-1 rounded">
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center p-10 text-gray-500">
                        Belum ada data slip gaji.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-5 flex items-center justify-between">
        <div class="text-sm text-gray-500">
            Menampilkan {{ $slips->firstItem() }} sampai {{ $slips->lastItem() }} dari {{ $slips->total() }} data
        </div>

        <div>
            {{ $slips->links() }}
        </div>
    </div>
</div>

@endsection