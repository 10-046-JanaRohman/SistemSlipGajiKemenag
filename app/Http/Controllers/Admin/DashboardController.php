<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GajiImportBatch;
use App\Models\Pegawai;
use App\Models\SlipGaji;

class DashboardController extends Controller
{
    public function index()
    {
        // ===========================================
        // Statistik Umum
        // ===========================================

        // Total pegawai
        $totalPegawai = Pegawai::count();

        // Total slip & gaji KESELURUHAN (akumulasi semua periode)
        $totalSlipKeseluruhan = SlipGaji::count();
        $totalGajiKeseluruhan = SlipGaji::sum('gaji_bersih');

        // Pegawai yang BELUM PERNAH punya slip sama sekali
        $pegawaiIdsDenganSlip = SlipGaji::distinct('pegawai_id')
            ->pluck('pegawai_id')
            ->toArray();

        $belumTerbit = max(0, $totalPegawai - count($pegawaiIdsDenganSlip));

        // ===========================================
        // Statistik Periode Import Terakhir
        // ===========================================

        $importTerakhir = GajiImportBatch::latest()->first();

        $totalSlip = 0;
        $totalGaji = 0;
        $rataRataGaji = 0;

        if ($importTerakhir) {
            $bulan = $importTerakhir->bulan;
            $tahun = $importTerakhir->tahun;

            $slips = SlipGaji::where('bulan', $bulan)
                ->where('tahun', $tahun);

            $totalSlip = $slips->count();
            $totalGaji = $slips->sum('gaji_bersih');
            $rataRataGaji = $totalSlip > 0 ? $totalGaji / $totalSlip : 0;
        }

        // ===========================================
        // Slip terbaru (5 terakhir)
        // ===========================================

        $slipTerbaru = SlipGaji::with('pegawai')
            ->latest('created_at')
            ->take(5)
            ->get();

        // ===========================================
        // Import terbaru (5 terakhir)
        // ===========================================

        $importTerbaru = GajiImportBatch::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPegawai',
            'totalSlipKeseluruhan',
            'totalGajiKeseluruhan',
            'totalSlip',
            'totalGaji',
            'rataRataGaji',
            'belumTerbit',
            'importTerakhir',
            'importTerbaru',
            'slipTerbaru'
        ));
    }
}