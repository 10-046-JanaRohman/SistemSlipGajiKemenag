<?php

namespace App\Jobs;

use App\Imports\GajiExcelImport;
use App\Models\GajiImportBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProcessGajiImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $batchId,
        public string $storedPath
    ) {}

    public function handle(): void
    {
        \Log::info('JOB START');

        $batch = GajiImportBatch::findOrFail($this->batchId);

        \Log::info('BATCH FOUND');

        $import = new GajiExcelImport($batch);

        \Log::info('IMPORT CLASS CREATED');

        $file = storage_path('app/public/' . $this->storedPath);

        \Log::info('FILE PATH', [
            'path' => $file,
            'exists' => file_exists($file),
        ]);

        Excel::import($import, $file);

        \Log::info('EXCEL IMPORT DONE');

        $batch->update([
            'jumlah_data' => $import->total,
            'berhasil'    => $import->success,
            'gagal'       => $import->failed,
        ]);

        \Log::info('JOB FINISHED');
    }
}