<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\SlipGaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatSlipController extends Controller
{
    public function index(Request $request)
    {
        $pegawai = Auth::user()->pegawai;

        $query = SlipGaji::where('pegawai_id', $pegawai->id)
            ->orderByDesc('tahun')
            ->orderByDesc('bulan');

        // Filter by tahun (year)
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Filter by bulan (month)
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        $slips = $query->paginate(10)->appends($request->all());

        // Get unique years for filter dropdown
        $tahunList = SlipGaji::where('pegawai_id', $pegawai->id)
            ->distinct()
            ->pluck('tahun')
            ->sortDesc()
            ->values();

        return view(
            'pegawai.riwayat.index',
            compact('pegawai', 'slips', 'tahunList')
        );
    }
}
