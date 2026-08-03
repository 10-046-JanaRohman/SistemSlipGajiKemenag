<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\SlipGaji;
use Illuminate\Http\Request;

class RiwayatSlipController extends Controller
{
    public function index(Request $request)
    {
        $query = SlipGaji::with('pegawai');

        // Filter by pegawai
        if ($request->filled('pegawai_id')) {
            $query->where('pegawai_id', $request->pegawai_id);
        }

        // Filter by bulan
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        // Filter by tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Search by nama pegawai
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $slips = $query->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->paginate(15)
            ->appends($request->all());

        // Data untuk filter
        $pegawaiList = Pegawai::orderBy('nama')->get();
        $tahunList = SlipGaji::distinct()->pluck('tahun')->sortDesc()->values();

        return view('admin.riwayat-slip.index', compact(
            'slips',
            'pegawaiList',
            'tahunList'
        ));
    }
}