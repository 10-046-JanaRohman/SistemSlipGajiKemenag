<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\SlipGaji;
use App\Services\SlipGajiCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SlipGajiController extends Controller
{
    public function index()
    {
        $slips = SlipGaji::with('pegawai')
            ->latest()
            ->paginate(10);

        return view('admin.slip-gaji.index', compact('slips'));
    }

    public function create()
    {
        $pegawais = Pegawai::orderBy('nama')->get();

        return view('admin.slip-gaji.create', compact('pegawais'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id'      => 'required|exists:pegawais,id',
            'bulan'           => 'required',
            'tahun'           => 'required|numeric',
            'tanggal_terbit'  => 'required|date',
            'gaji_pokok'      => 'required|numeric',
            'tunjangan'       => 'required|numeric',
            'potongan'        => 'required|numeric',
        ]);

        $gajiBersih =
            $request->gaji_pokok +
            $request->tunjangan -
            $request->potongan;

        SlipGaji::create([
            'pegawai_id'      => $request->pegawai_id,
            'bulan'           => $request->bulan,
            'tahun'           => $request->tahun,
            'tanggal_terbit'  => $request->tanggal_terbit,
            'gaji_pokok'      => $request->gaji_pokok,
            'tunjangan'       => $request->tunjangan,
            'potongan'        => $request->potongan,
            'gaji_bersih'     => $gajiBersih,
        ]);

        return redirect()
            ->route('slip-gaji.index')
            ->with('success', 'Slip gaji berhasil ditambahkan.');
    }

    public function show(SlipGaji $slip_gaji)
    {
        $slip_gaji->load('pegawai');

        $rincian = SlipGajiCalculator::hitung(
            $slip_gaji->detail_gaji ?? []
        );

        return view(
            'admin.slip-gaji.show',
            compact('slip_gaji', 'rincian')
        );
    }

    public function pdf(SlipGaji $slip_gaji)
    {
        $slip_gaji->load('pegawai');

        $rincian = SlipGajiCalculator::hitung(
            $slip_gaji->detail_gaji ?? []
        );

        $pdf = Pdf::loadView(
            'pdf.slip-gaji',
            [
                'slip' => $slip_gaji,
                'rincian' => $rincian,
            ]
        )->setPaper('a4', 'landscape');

        $namaFile =
            'slip-gaji-' .
            ($slip_gaji->pegawai->nip ?? 'pegawai') .
            '-' .
            $slip_gaji->bulan .
            '-' .
            $slip_gaji->tahun .
            '.pdf';

        return $pdf->download($namaFile);
    }

    public function edit(SlipGaji $slip_gaji)
    {
        $pegawais = Pegawai::orderBy('nama')->get();

        return view('admin.slip-gaji.edit', compact('slip_gaji', 'pegawais'));
    }

    public function update(Request $request, SlipGaji $slip_gaji)
    {
        $request->validate([
            'pegawai_id'      => 'required|exists:pegawais,id',
            'bulan'           => 'required',
            'tahun'           => 'required|numeric',
            'tanggal_terbit'  => 'required|date',
            'gaji_pokok'      => 'required|numeric',
            'tunjangan'       => 'required|numeric',
            'potongan'        => 'required|numeric',
        ]);

        $slip_gaji->update([
            'pegawai_id'      => $request->pegawai_id,
            'bulan'           => $request->bulan,
            'tahun'           => $request->tahun,
            'tanggal_terbit'  => $request->tanggal_terbit,
            'gaji_pokok'      => $request->gaji_pokok,
            'tunjangan'       => $request->tunjangan,
            'potongan'        => $request->potongan,
            'gaji_bersih'     =>
                $request->gaji_pokok +
                $request->tunjangan -
                $request->potongan,
        ]);

        return redirect()
            ->route('slip-gaji.index')
            ->with('success', 'Slip gaji berhasil diubah.');
    }

    public function destroy(SlipGaji $slip_gaji)
    {
        $slip_gaji->delete();

        return redirect()
            ->route('slip-gaji.index')
            ->with('success', 'Slip gaji berhasil dihapus.');
    }
}
