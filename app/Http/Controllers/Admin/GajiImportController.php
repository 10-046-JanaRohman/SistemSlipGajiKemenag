<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessGajiImportJob;
use App\Models\GajiImportBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GajiImportController extends Controller
{
    public function create()
    {
        return view('admin.gaji-imports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required|string|max:20',
            'tahun' => 'required|digits:4',
            'file_excel' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file_excel');
        $path = $file->store('imports/gaji', 'public');

        Log::info('Import batch created', [
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'file' => $file->getClientOriginalName(),
            'user' => auth()->id(),
        ]);

        $batch = GajiImportBatch::create([
            'uploaded_by' => auth()->id(),
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'nama_file' => $file->getClientOriginalName(),
            'lokasi_file' => $path,
            'jumlah_data' => 0,
            'berhasil' => 0,
            'gagal' => 0,
        ]);

        Log::info('Import batch saved', [
            'batch_id' => $batch->id,
            'bulan' => $batch->bulan,
            'tahun' => $batch->tahun,
        ]);

        ProcessGajiImportJob::dispatch($batch->id, $path);

        return redirect()
            ->route('slip-gaji.index')
            ->with('success', 'File masuk antrean import. Proses berjalan di background.');
    }
}