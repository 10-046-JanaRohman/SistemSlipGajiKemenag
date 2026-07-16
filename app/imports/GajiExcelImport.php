<?php

namespace App\Imports;

use App\Models\GajiImportBatch;
use App\Services\GajiImportRowProcessor;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GajiExcelImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public int $total = 0;
    public int $success = 0;
    public int $failed = 0;

    protected GajiImportBatch $batch;

    public function __construct(GajiImportBatch $batch)
    {
        $this->batch = $batch;
    }

    public function chunkSize(): int
    {
        return 50;
    }

    public function collection(Collection $rows)
    {
        $this->total += $rows->count();

        foreach ($rows as $row) {

            $data = collect($row)
                ->mapWithKeys(function ($value, $key) {
                    return [
                        strtolower(trim((string) $key)) => $value,
                    ];
                })
                ->toArray();

            $imported = app(GajiImportRowProcessor::class)->process($data, $this->batch);

            if ($imported) {
                $this->success++;
            } else {
                $this->failed++;
            }
        }
    }
}
