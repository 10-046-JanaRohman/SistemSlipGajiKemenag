<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\SlipGaji;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $term = $validated['q'];
        $normalizedTerm = mb_strtolower(trim((string) preg_replace('/\s+/', ' ', $term)));
        $user = $request->user();

        if (in_array($user->role, ['admin', 'super_admin'], true)) {
            $pegawai = Pegawai::query()
                ->where(fn ($query) => $query->where('nama', 'like', "%{$term}%")->orWhere('nip', 'like', "%{$term}%"))
                ->limit(5)
                ->get(['id', 'nama', 'nip'])
                ->map(fn (Pegawai $item) => [
                    'type' => 'pegawai',
                    'id' => $item->id,
                    'title' => $item->nama,
                    'subtitle' => "NIP: {$item->nip}",
                    'url' => '/admin/pegawai?search='.urlencode($item->nip),
                ]);

            $slips = SlipGaji::with('pegawai:id,nama,nip')
                ->whereHas('pegawai', fn ($query) => $query->where('nama', 'like', "%{$term}%")->orWhere('nip', 'like', "%{$term}%"))
                ->latest('tahun')
                ->latest('bulan')
                ->limit(5)
                ->get()
                ->map(fn (SlipGaji $item) => [
                    'type' => 'slip',
                    'id' => $item->id,
                    'title' => "Slip gaji {$item->pegawai?->nama}",
                    'subtitle' => "{$item->bulan}/{$item->tahun} · NIP: {$item->pegawai?->nip}",
                    'url' => '/admin/slip-gaji?search='.urlencode($item->pegawai?->nip ?? ''),
                ]);

            return response()->json(['success' => true, 'data' => $pegawai->concat($slips)->values()]);
        }

        $pegawai = $user->pegawai;
        $slips = $pegawai
            ? SlipGaji::where('pegawai_id', $pegawai->id)->latest('tahun')->latest('bulan')->get()
                ->filter(function (SlipGaji $item) use ($normalizedTerm) {
                    $periode = mb_strtolower(trim("{$item->bulan} {$item->tahun}"));
                    $periodeDenganPemisah = mb_strtolower("{$item->bulan}/{$item->tahun}");

                    return str_contains($periode, $normalizedTerm)
                        || str_contains($periodeDenganPemisah, $normalizedTerm);
                })
                ->take(5)
            : collect();

        return response()->json(['success' => true, 'data' => $slips->map(fn (SlipGaji $item) => [
            'type' => 'slip',
            'id' => $item->id,
            'title' => "Slip gaji {$item->bulan}/{$item->tahun}",
            'subtitle' => 'Lihat rincian slip gaji',
            'url' => "/user/slip/{$item->id}",
        ])->values()]);
    }
}
