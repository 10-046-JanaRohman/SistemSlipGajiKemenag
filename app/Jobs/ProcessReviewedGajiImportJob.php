<?php

namespace App\Jobs;

use App\Models\GajiImportBatch;
use App\Models\Notifikasi;
use App\Models\SlipGaji;
use App\Services\GajiImportRowProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

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

        $batch->update(['status' => 'processing']);

        $path = "import-reviews/{$this->reviewToken}.json";

        if (! Storage::disk('local')->exists($path)) {
            $batch->update([
                'berhasil' => 0,
                'gagal' => $batch->jumlah_data ?: 0,
                'status' => 'failed',
            ]);
            $this->notifyImportFailure($batch);
            return;
        }

        $draft = json_decode(Storage::disk('local')->get($path), true) ?: [];
        $reviewRows = collect($draft['rows'] ?? []);
        $rows = $reviewRows
            ->filter(fn ($row) => ($row['valid'] ?? false) && is_array($row['data'] ?? null) && ! empty($row['data']))
            ->values();

        $success = 0;
        $created = 0;
        $updated = 0;
        $failureLog = collect($batch->log_gagal ?? []);

        foreach ($rows as $row) {
            $result = $processor->process($row['data'], $batch);

            if ($result === GajiImportRowProcessor::CREATED) {
                $success++;
                $created++;
                continue;
            }

            if ($result === GajiImportRowProcessor::UPDATED) {
                $success++;
                $updated++;
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
            'ditambahkan' => $created,
            'diperbarui' => $updated,
            'gagal' => $failureLog->count(),
            'log_gagal' => $failureLog->values()->all(),
            'status' => 'completed',
        ]);

        Notifikasi::create([
            'user_id' => $batch->uploaded_by,
            'judul' => 'Import gaji selesai diproses',
            'isi' => "Periode {$batch->bulan} {$batch->tahun}. Ditambahkan: {$created}. Diperbarui: {$updated}. Gagal: ".$failureLog->count().'.',
        ]);

        // Beri tahu setiap pegawai yang slipnya berhasil tersedia pada impor ini.
        $employeeUserIds = SlipGaji::query()
            ->with('pegawai:id,user_id')
            ->where('import_batch_id', $batch->id)
            ->get()
            ->pluck('pegawai.user_id')
            ->filter()
            ->unique()
            ->values();

        if ($employeeUserIds->isNotEmpty()) {
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

        // Draft tidak lagi aktif setelah seluruh baris selesai diproses.
        Storage::disk('local')->delete($path);
    }

    public function failed(Throwable $exception): void
    {
        $batch = GajiImportBatch::find($this->batchId);

        if ($batch) {
            $batch->update(['status' => 'failed']);
            $this->notifyImportFailure($batch);
        }
    }

    private function notifyImportFailure(GajiImportBatch $batch): void
    {
        Notifikasi::create([
            'user_id' => $batch->uploaded_by,
            'judul' => 'Import gaji gagal diproses',
            'isi' => "Periode {$batch->bulan} {$batch->tahun} gagal diproses. Silakan periksa riwayat import.",
        ]);
    }
}
