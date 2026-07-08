<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SlipGaji;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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
        if ($user->role === 'admin' && $request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $slips = $query->latest()
            ->paginate(10);

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

        // Filter by tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Filter by bulan
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        $slips = $query->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat slip berhasil diambil.',
            'data' => $slips,
        ]);
    }

    public function pdf(SlipGaji $slipGaji)
    {
        $slipGaji->load('pegawai');

        $rincian = SlipGajiFormatter::format(
            $slipGaji->detail_gaji ?? []
        );

        $pdf = Pdf::loadView('pdf.slip-gaji', [

            'slip' => $slipGaji,

            'rincian' => $rincian,

        ])->setPaper('a4', 'portrait');

        $namaFile = 'slip-gaji-'
            . ($slipGaji->pegawai->nip ?? 'pegawai')
            . '-'
            . $slipGaji->bulan
            . '-'
            . $slipGaji->tahun
            . '.pdf';

        return $pdf->download($namaFile);
    }
}
