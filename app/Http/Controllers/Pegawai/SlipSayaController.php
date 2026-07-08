<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\SlipGaji;
use App\Services\SlipGajiFormatter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class SlipSayaController extends Controller
{
    public function index()
    {
        $pegawai = Auth::user()->pegawai;

        $slips = SlipGaji::where('pegawai_id', $pegawai->id)
            ->latest('tahun')
            ->latest('bulan')
            ->paginate(10);

        return view('pegawai.slip.index', compact(
            'pegawai',
            'slips'
        ));
    }

    public function show(SlipGaji $slip)
    {
        $pegawai = Auth::user()->pegawai;

        if ($slip->pegawai_id != $pegawai->id) {
            abort(403);
        }

        $slip->load('pegawai');

        $rincian = SlipGajiFormatter::format(
            $slip->detail_gaji ?? []
        );

        return view('pegawai.slip.show', compact(
            'slip',
            'rincian'
        ));
    }

    public function pdf(SlipGaji $slip)
    {
        $pegawai = Auth::user()->pegawai;

        if ($slip->pegawai_id != $pegawai->id) {
            abort(403);
        }

        $slip->load('pegawai');

        $rincian = SlipGajiFormatter::format(
            $slip->detail_gaji ?? []
        );

        $pdf = Pdf::loadView('pdf.slip-gaji', [
            'slip' => $slip,
            'rincian' => $rincian,
        ])->setPaper('a4', 'portrait');

        return $pdf->download(
            'slip-gaji-' .
            $slip->pegawai->nip .
            '-' .
            $slip->bulan .
            '-' .
            $slip->tahun .
            '.pdf'
        );
    }
}