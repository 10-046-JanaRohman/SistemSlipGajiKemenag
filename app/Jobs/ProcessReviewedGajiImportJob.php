<?php

namespace App\Jobs;

use App\Models\GajiImportBatch;
use App\Models\Notifikasi;
use App\Services\GajiImportRowProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessReviewedGajiImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $batchId,
        public string $reviewToken
    ) {}

    public function handle(GajiImportRowProcessor $processor): void
    {
        $batch = GajiImportBatch::find($this->batchId);

        if (! $batch) {
            return;
        }

        $path = "import-reviews/{$this->reviewToken}.json";

        if (! Storage::disk('local')->exists($path)) {
            $batch->update([
                'berhasil' => 0,
                'gagal' => $batch->jumlah_data ?: 0,
            ]);
            return;
        }

        $draft = json_decode(Storage::disk('local')->get($path), true) ?: [];
        $reviewRows = collect($draft['rows'] ?? []);
        $rows = $reviewRows
            ->filter(fn ($row) => ($row['valid'] ?? false) && is_array($row['data'] ?? null) && ! empty($row['data']))
            ->values();

        $success = 0;
        $failureLog = collect($batch->log_gagal ?? []);

        foreach ($rows as $row) {
            if ($processor->process($row['data'], $batch)) {
                $success++;
                continue;
            }

            $failureLog->push([
                'baris' => $row['row_number'] ?? null,
                'keterangan' => 'Gagal diproses oleh sistem. Periksa log aplikasi untuk rincian.',
            ]);
        }

        $batch->update([
            'jumlah_data' => $reviewRows->count(),
            'berhasil' => $success,
            'gagal' => $failureLog->count(),
            'log_gagal' => $failureLog->values()->all(),
        ]);

        Notifikasi::create([
            'user_id' => $batch->uploaded_by,
            'judul' => 'Import gaji selesai diproses',
            'isi' => "Berhasil: {$success} baris. Gagal: ".$failureLog->count().' baris.',
        ]);
    }
}
