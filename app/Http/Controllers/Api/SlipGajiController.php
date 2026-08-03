<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SlipGaji;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Services\SlipGajiFormatter;

class SlipGajiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = SlipGaji::with('pegawai');

        // Jika pegawai, hanya tampilkan slip miliknya
        if ($user->role === 'pegawai') {
            $pegawai = $user->pegawai;
            if ($pegawai) {
                $query->where('pegawai_id', $pegawai->id);
            } else {
                // Jika pegawai belum memiliki data pegawai, return empty
                return response()->json([
                    'success' => true,
                    'message' => 'Data slip gaji berhasil diambil.',
                    'data' => [],
                ]);
            }
        }

        // Filter by bulan (opsional)
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        // Filter by tahun (opsional)
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Search by nama pegawai (hanya untuk admin)
        if (in_array($user->role, ['admin', 'super_admin'], true) && $request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $slips = $query->latest()
            ->paginate(10);

        $this->appendCalculatedTotals($slips);

        return response()->json([
            'success' => true,
            'message' => 'Data slip gaji berhasil diambil.',
            'data' => $slips,
        ]);
    }

    public function show(Request $request, SlipGaji $slipGaji)
    {
        $user = $request->user();

        // Load relasi pegawai
        $slipGaji->load('pegawai');

        // Jika pegawai, cek apakah slip miliknya
        if ($user->role === 'pegawai') {
            $pegawai = $user->pegawai;
            if (!$pegawai || $slipGaji->pegawai_id !== $pegawai->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke slip ini.',
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail slip gaji.',
            'data' => [
                'slip' => $slipGaji,
                'rincian' => SlipGajiFormatter::format(
                    $slipGaji->detail_gaji ?? []
                ),
            ]
        ]);
    }

    public function riwayat(Request $request)
    {
        $user = $request->user();
        
        if ($user->role === 'pegawai') {
            $pegawai = $user->pegawai;
            if (!$pegawai) {
                return response()->json([
                    'success' => true,
                    'message' => 'Riwayat slip berhasil diambil.',
                    'data' => [],
                ]);
            }

            $query = SlipGaji::where('pegawai_id', $pegawai->id);
        } else {
            // Admin bisa melihat semua
            $query = SlipGaji::with('pegawai');
        }

        if ($user->role !== 'pegawai' && $request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Filter by tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Filter by bulan
        if ($request->filled('bulan')) {
            $bulan = $request->bulan;
            $bulanMap = [
                '1' => 'Januari',
                '2' => 'Februari',
                '3' => 'Maret',
                '4' => 'April',
                '5' => 'Mei',
                '6' => 'Juni',
                '7' => 'Juli',
                '8' => 'Agustus',
                '9' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember',
            ];
            $bulanValues = [$bulan];

            if (isset($bulanMap[(string) $bulan])) {
                $bulanValues[] = $bulanMap[(string) $bulan];
            }

            $bulanIndex = array_search($bulan, $bulanMap, true);
            if ($bulanIndex !== false) {
                $bulanValues[] = $bulanIndex;
            }

            $query->whereIn('bulan', array_unique($bulanValues));
        }

        $slips = $query->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->paginate(10);

        $this->appendCalculatedTotals($slips);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat slip berhasil diambil.',
            'data' => $slips,
        ]);
    }

    public function pdf(Request $request, SlipGaji $slipGaji)
    {
        $this->authorizePdfAccess($request, $slipGaji);

        return $this->buildPdf($slipGaji)->stream($this->pdfFilename($slipGaji));
    }

    public function pdfUrl(Request $request, SlipGaji $slipGaji)
    {
        $this->authorizePdfAccess($request, $slipGaji);

        $path = URL::temporarySignedRoute(
            'slip-gaji.pdf-download',
            now()->addMinutes(5),
            ['slipGaji' => $slipGaji->id],
            false
        );

        return response()->json([
            'success' => true,
            'data' => [
                'url' => rtrim($request->getSchemeAndHttpHost(), '/') . $path,
            ],
        ]);
    }

    public function pdfDownload(SlipGaji $slipGaji)
    {
        return $this->buildPdf($slipGaji)->download($this->pdfFilename($slipGaji));
    }

    private function authorizePdfAccess(Request $request, SlipGaji $slipGaji): void
    {
        $user = $request->user();

        if ($user->role === 'pegawai') {
            $pegawai = $user->pegawai;

            abort_unless($pegawai && $slipGaji->pegawai_id === $pegawai->id, 403, 'Anda tidak memiliki akses ke slip ini.');
        }
    }

    private function buildPdf(SlipGaji $slipGaji)
    {
        $slipGaji->load('pegawai');

        return Pdf::loadView('pdf.slip-gaji', [
            'slip' => $slipGaji,
            'rincian' => SlipGajiFormatter::format($slipGaji->detail_gaji ?? []),
        ])->setPaper('a4', 'landscape');
    }

    private function pdfFilename(SlipGaji $slipGaji): string
    {
        return 'slip-gaji-'
            . ($slipGaji->pegawai->nip ?? 'pegawai')
            . '-'
            . $slipGaji->bulan
            . '-'
            . $slipGaji->tahun
            . '.pdf';
    }

    private function appendCalculatedTotals($paginator): void
    {
        $paginator->getCollection()->transform(function (SlipGaji $slip) {
            $rincian = SlipGajiFormatter::format($slip->detail_gaji ?? []);
            $gajiBersih = $rincian['gaji_bersih'] ?? $slip->gaji_bersih;

            $slip->setAttribute('gaji_bersih_hitung', $gajiBersih);
            $slip->setAttribute('total_gaji', $gajiBersih);
            $slip->setAttribute('total_pendapatan', $rincian['total_pendapatan'] ?? null);
            $slip->setAttribute('total_potongan', $rincian['total_potongan'] ?? null);
            $slip->setAttribute('status', $slip->tanggal_terbit ? 'Sudah Dibagikan' : 'Belum Dibagikan');
            $slip->setAttribute('dibagikan', $slip->tanggal_terbit ? 1 : 0);
            $slip->setAttribute('tanggal_dibagikan', $slip->tanggal_terbit);

            return $slip;
        });
    }
}
