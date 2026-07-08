@extends('layouts.pegawai')

@section('title','Slip Saya')

@section('content')

<div class="max-w-6xl mx-auto">

    <div class="mb-8">

        <h2 class="text-3xl font-bold">
            Slip Gaji Saya
        </h2>

        <p class="text-gray-500 mt-2">
            Daftar seluruh slip gaji yang telah diterbitkan.
        </p>

    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden">

        <table class="w-full">

            <thead class="bg-green-700 text-white">

                <tr>

                    <th class="px-6 py-4 text-left">
                        Periode
                    </th>

                    <th class="px-6 py-4 text-left">
                        Tanggal Terbit
                    </th>

                    <th class="px-6 py-4 text-center">
                        Status
                    </th>

                    <th class="px-6 py-4 text-center">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

            @forelse($slips as $slip)

                <tr class="border-b hover:bg-gray-50">

                    <td class="px-6 py-4">
                        {{ $slip->bulan }}
                        {{ $slip->tahun }}
                    </td>

                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($slip->tanggal_terbit)->format('d M Y') }}
                    </td>

                    <td class="px-6 py-4 text-center">

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

                    <td colspan="4"
                        class="text-center py-10 text-gray-400">

                        Belum ada slip gaji.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-6">

        {{ $slips->links() }}

    </div>

</div>

@endsection