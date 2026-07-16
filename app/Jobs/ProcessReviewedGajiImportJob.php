<?php

namespace App\Jobs;

use App\Models\GajiImportBatch;
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
        $rows = collect($draft['rows'] ?? [])
            ->map(fn ($row) => $row['data'] ?? [])
            ->filter(fn ($row) => is_array($row) && ! empty($row))
            ->values();

        $success = 0;
        $failed = 0;

        foreach ($rows as $row) {
            $processor->process($row, $batch) ? $success++ : $failed++;
        }

        $batch->update([
            'jumlah_data' => $rows->count(),
            'berhasil' => $success,
            'gagal' => $failed,
        ]);
    }
}
