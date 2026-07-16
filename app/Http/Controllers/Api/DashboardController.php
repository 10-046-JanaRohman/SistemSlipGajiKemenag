<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\SlipGaji;
use App\Models\User;
use App\Services\SlipGajiFormatter;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Jika pegawai, tampilkan dashboard pegawai
        if (in_array($user->role, ['pegawai', 'user'], true)) {
            $pegawai = $user->pegawai;

            if (!$pegawai) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dashboard berhasil diambil.',
                    'data' => [
                        'pegawai' => null,
                        'total_slip' => 0,
                        'gaji_terakhir' => 0,
                        'slip_terakhir' => null,
                        'status_slip' => 'Belum Ada Slip',
                    ],
                ]);
            }

            $slips = SlipGaji::where('pegawai_id', $pegawai->id);
            $slipTerakhir = $slips->orderByDesc('tahun')->orderByDesc('bulan')->first();
            $totalSlip = $slips->count();
            $gajiTerakhir = $slipTerakhir?->gaji_bersih ?? 0;
            $statusSlip = $slipTerakhir ? 'Sudah Terbit' : 'Belum Ada Slip';

            return response()->json([
                'success' => true,
                'message' => 'Dashboard berhasil diambil.',
                'data' => [
                    'pegawai' => $pegawai,
                    'total_slip' => $totalSlip,
                    'gaji_terakhir' => $gajiTerakhir,
                    'slip_terakhir' => $slipTerakhir,
                    'status_slip' => $statusSlip,
                ],
            ]);
        }

        // Jika admin, tampilkan dashboard admin
        $totalPegawai = Pegawai::count();
        $totalSlipKeseluruhan = SlipGaji::count();
        $totalGajiKeseluruhan = $this->sumGajiBersih(SlipGaji::query());

        // Pegawai yang BELUM PERNAH punya slip sama sekali
        $pegawaiIdsDenganSlip = SlipGaji::select('pegawai_id')
            ->distinct()
            ->pluck('pegawai_id')
            ->toArray();

        $belumTerbit = max(0, $totalPegawai - count($pegawaiIdsDenganSlip));

        $importTerakhir = \App\Models\GajiImportBatch::latest()->first();

        $totalSlip = 0;
        $totalGaji = 0;

        if ($importTerakhir) {
            $bulan = $importTerakhir->bulan;
            $tahun = $importTerakhir->tahun;

            $totalSlip = SlipGaji::where('bulan', $bulan)->where('tahun', $tahun)->count();
            $totalGaji = $this->sumGajiBersih(
                SlipGaji::where('bulan', $bulan)->where('tahun', $tahun)
            );
        }

        $slipTerbaru = SlipGaji::with('pegawai')
            ->latest('tanggal_terbit')
            ->take(5)
            ->get()
            ->map(function (SlipGaji $slip) {
                $rincian = SlipGajiFormatter::format($slip->detail_gaji ?? []);
                $gajiBersih = $rincian['gaji_bersih'] ?? $slip->gaji_bersih;

                $slip->setAttribute('gaji_bersih_hitung', $gajiBersih);
                $slip->setAttribute('total_gaji', $gajiBersih);

                return $slip;
            });

        return response()->json([
            'success' => true,
            'message' => 'Dashboard berhasil diambil.',
            'data' => [
                'total_pegawai' => $totalPegawai,
                'total_slip_keseluruhan' => $totalSlipKeseluruhan,
                'total_gaji_keseluruhan' => $totalGajiKeseluruhan,
                'total_slip_periode' => $totalSlip,
                'total_gaji_periode' => $totalGaji,
                'belum_terbit' => $belumTerbit,
                'import_terakhir' => $importTerakhir,
                'slip_terbaru' => $slipTerbaru,
            ],
        ]);
    }

    private function sumGajiBersih($query): float
    {
        return $query->get()
            ->sum(function (SlipGaji $slip) {
                $rincian = SlipGajiFormatter::format($slip->detail_gaji ?? []);

                return $rincian['gaji_bersih'] ?? $slip->gaji_bersih ?? 0;
            });
    }
}
