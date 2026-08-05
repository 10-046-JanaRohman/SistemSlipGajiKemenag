<?php

namespace App\Jobs;

use App\Imports\GajiExcelImport;
use App\Models\GajiImportBatch;
use App\Models\Notifikasi;
use App\Models\SlipGaji;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

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
        $batch->update(['status' => 'processing']);

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
            'ditambahkan' => $import->created,
            'diperbarui'  => $import->updated,
            'gagal'       => $import->failed,
            'status'      => 'completed',
        ]);

        $this->notifyImportResult($batch);

        \Log::info('JOB FINISHED');
    }

    public function failed(Throwable $exception): void
    {
        $batch = GajiImportBatch::find($this->batchId);

        if (! $batch) {
            return;
        }

        $batch->update(['status' => 'failed']);

        Notifikasi::create([
            'user_id' => $batch->uploaded_by,
            'judul' => 'Import gaji gagal diproses',
            'isi' => "Periode {$batch->bulan} {$batch->tahun} gagal diproses. Silakan periksa riwayat import.",
        ]);
    }

    private function notifyImportResult(GajiImportBatch $batch): void
    {
        Notifikasi::create([
            'user_id' => $batch->uploaded_by,
            'judul' => 'Import gaji selesai diproses',
            'isi' => "Periode {$batch->bulan} {$batch->tahun}. Ditambahkan: {$batch->ditambahkan}. Diperbarui: {$batch->diperbarui}. Gagal: {$batch->gagal}.",
        ]);

        $employeeUserIds = SlipGaji::query()
            ->with('pegawai:id,user_id')
            ->where('import_batch_id', $batch->id)
            ->get()
            ->pluck('pegawai.user_id')
            ->filter()
            ->unique()
            ->values();

        if ($employeeUserIds->isEmpty()) {
            return;
        }

        $now = now();
        Notifikasi::insert($employeeUserIds->map(fn ($userId) => [
            'user_id' => $userId,
            'judul' => 'Slip gaji tersedia',
            'isi' => "Slip gaji periode {$batch->bulan} {$batch->tahun} telah tersedia.",
            'dibaca' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all());
    }
}
