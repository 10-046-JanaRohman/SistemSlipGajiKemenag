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
        /*
        |--------------------------------------------------------------------------
        | Total Pegawai
        |--------------------------------------------------------------------------
        */

        $totalPegawai = Pegawai::count();

        /*
        |--------------------------------------------------------------------------
        | Total Slip Keseluruhan
        |--------------------------------------------------------------------------
        */

        $totalSlipKeseluruhan = SlipGaji::count();

        /*
        |--------------------------------------------------------------------------
        | Total Gaji Keseluruhan
        |--------------------------------------------------------------------------
        */

        $totalGajiKeseluruhan = SlipGaji::sum('gaji_bersih');

        /*
        |--------------------------------------------------------------------------
        | Import Terakhir
        |--------------------------------------------------------------------------
        */

        $importTerakhir = GajiImportBatch::latest()->first();

        /*
        |--------------------------------------------------------------------------
        | Statistik Periode Terakhir
        |--------------------------------------------------------------------------
        */

        $totalSlip = 0;
        $totalGaji = 0;
        $belumTerbit = 0;
        $rataRataGaji = 0;

        if ($importTerakhir) {

            $bulan = $importTerakhir->bulan;
            $tahun = $importTerakhir->tahun;

            $slips = SlipGaji::where('bulan', $bulan)
                ->where('tahun', $tahun);

            $totalSlip = $slips->count();

            $totalGaji = $slips->sum('gaji_bersih');

            $rataRataGaji = $totalSlip > 0 ? $totalGaji / $totalSlip : 0;

            $belumTerbit = max(
                0,
                $totalPegawai - $totalSlip
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Slip terbaru
        |--------------------------------------------------------------------------
        */

        $slipTerbaru = SlipGaji::with('pegawai')
            ->latest('tanggal_terbit')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Import terbaru (5 terakhir)
        |--------------------------------------------------------------------------
        */

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