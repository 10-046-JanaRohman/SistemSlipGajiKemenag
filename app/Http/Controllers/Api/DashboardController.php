<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\SlipGaji;
use App\Models\User;
use App\Services\SlipGajiFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $sudahDibagikanPeriode = 0;
        $belumDibagikanPeriode = $totalPegawai;

        if ($importTerakhir) {
            $bulan = $importTerakhir->bulan;
            $tahun = $importTerakhir->tahun;

            $slipPeriode = SlipGaji::where('bulan', $bulan)->where('tahun', $tahun);

            $totalSlip = (clone $slipPeriode)->count();
            $totalGaji = $this->sumGajiBersih(
                $slipPeriode
            );
            $sudahDibagikanPeriode = (clone $slipPeriode)
                ->whereNotNull('pegawai_id')
                ->distinct()
                ->count('pegawai_id');
            $belumDibagikanPeriode = max(0, $totalPegawai - $sudahDibagikanPeriode);
        }

        $ringkasanPeriode = SlipGaji::query()
            ->select('bulan', 'tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->orderByDesc(DB::raw('CAST(bulan AS UNSIGNED)'))
            ->limit(6)
            ->get()
            ->map(function (SlipGaji $periode) {
                $slips = SlipGaji::query()
                    ->where('bulan', $periode->bulan)
                    ->where('tahun', $periode->tahun);

                return [
                    'bulan' => (int) $periode->bulan,
                    'tahun' => (int) $periode->tahun,
                    'total_gaji' => $this->sumGajiBersih($slips),
                    'slip_dibagikan' => (clone $slips)->distinct()->count('pegawai_id'),
                    'terakhir_diperbarui' => (clone $slips)->max('updated_at'),
                ];
            })
            ->values();

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
                'periode_aktif' => $importTerakhir ? [
                    'bulan' => (int) $importTerakhir->bulan,
                    'tahun' => (int) $importTerakhir->tahun,
                ] : null,
                'sudah_dibagikan_periode' => $sudahDibagikanPeriode,
                'belum_dibagikan_periode' => $belumDibagikanPeriode,
                'belum_terbit' => $belumTerbit,
                'import_terakhir' => $importTerakhir,
                'ringkasan_gaji_periode' => $ringkasanPeriode,
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
