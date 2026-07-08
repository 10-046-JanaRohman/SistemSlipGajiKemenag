<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\SlipGaji;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $pegawai = Auth::user()->pegawai;

        // Semua slip pegawai
        $slips = SlipGaji::where('pegawai_id', $pegawai->id);

        // Slip terakhir
        $slipTerakhir = $slips
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->first();

        // Jumlah seluruh slip
        $totalSlip = SlipGaji::where('pegawai_id', $pegawai->id)->count();

        // Total gaji terakhir
        $gajiTerakhir = $slipTerakhir?->gaji_bersih ?? 0;

        // Status slip
        $statusSlip = $slipTerakhir ? 'Sudah Terbit' : 'Belum Ada Slip';

        return view('pegawai.dashboard', compact(
            'pegawai',
            'totalSlip',
            'slipTerakhir',
            'gajiTerakhir',
            'statusSlip'
        ));
    }
}