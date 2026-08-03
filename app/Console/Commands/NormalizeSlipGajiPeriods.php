<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NormalizeSlipGajiPeriods extends Command
{
    protected $signature = 'slip-gaji:normalize-periods {--apply : Terapkan normalisasi dan hapus duplikasi setelah membuat backup}';

    protected $description = 'Menstandarkan bulan slip menjadi 1-12 dan membersihkan slip duplikat per pegawai/periode.';

    private const MONTHS = [
        'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
        'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
        'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
    ];

    public function handle(): int
    {
        $rows = DB::table('slip_gajis')->select('id', 'bulan')->orderBy('id')->get();
        $normalizations = [];
        $invalidMonths = [];

        foreach ($rows as $row) {
            $month = $this->normalizeMonth($row->bulan);

            if ($month === null) {
                $invalidMonths[] = ['id' => $row->id, 'bulan' => $row->bulan];
                continue;
            }

            if ((string) $row->bulan !== (string) $month) {
                $normalizations[$row->id] = $month;
            }
        }

        $duplicateGroups = $this->duplicateGroups($normalizations);
        $excessSlips = $duplicateGroups->sum(fn ($group) => $group->count() - 1);

        $this->info("Slip diperiksa: {$rows->count()}");
        $this->info('Bulan yang akan dinormalisasi: '.count($normalizations));
        $this->info('Kelompok duplikat: '.$duplicateGroups->count());
        $this->info("Slip duplikat yang akan diarsipkan/dihapus: {$excessSlips}");

        if ($invalidMonths !== []) {
            $this->error('Ditemukan bulan yang tidak dikenali. Proses dibatalkan.');
            $this->table(['ID Slip', 'Nilai Bulan'], array_slice($invalidMonths, 0, 20));

            return self::FAILURE;
        }

        if (! $this->option('apply')) {
            $this->comment('Mode audit: data belum diubah. Jalankan dengan --apply untuk menerapkan perbaikan.');

            return self::SUCCESS;
        }

        $backupPath = $this->backupSlips();
        $this->info("Backup data slip dibuat: storage/app/{$backupPath}");

        DB::transaction(function () use ($normalizations, $duplicateGroups) {
            foreach ($normalizations as $id => $month) {
                DB::table('slip_gajis')->where('id', $id)->update(['bulan' => $month]);
            }

            foreach ($duplicateGroups as $group) {
                $ids = $group
                    ->sortByDesc(fn ($slip) => sprintf('%s-%020d', $slip->tanggal_terbit ?? '0000-00-00', $slip->id))
                    ->pluck('id')
                    ->values();

                $ids->shift(); // Pertahankan slip terbaru.

                if ($ids->isNotEmpty()) {
                    DB::table('slip_gajis')->whereIn('id', $ids)->delete();
                }
            }
        });

        $this->info('Normalisasi dan pembersihan duplikasi selesai.');

        return self::SUCCESS;
    }

    private function duplicateGroups(array $normalizations)
    {
        return DB::table('slip_gajis')
            ->select('id', 'pegawai_id', 'bulan', 'tahun', 'tanggal_terbit')
            ->orderBy('id')
            ->get()
            ->map(function ($slip) use ($normalizations) {
                $slip->bulan = $normalizations[$slip->id] ?? $this->normalizeMonth($slip->bulan);

                return $slip;
            })
            ->groupBy(fn ($slip) => implode('|', [$slip->pegawai_id, $slip->bulan, $slip->tahun]))
            ->filter(fn ($group) => $group->count() > 1);
    }

    private function backupSlips(): string
    {
        $path = 'backups/slip-gaji-before-normalization-'.now()->format('Ymd-His').'.json';

        Storage::disk('local')->put($path, json_encode([
            'created_at' => now()->toIso8601String(),
            'table' => 'slip_gajis',
            'rows' => DB::table('slip_gajis')->orderBy('id')->get(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $path;
    }

    private function normalizeMonth(mixed $value): ?int
    {
        $month = strtolower(trim((string) $value));

        if (is_numeric($month) && (int) $month >= 1 && (int) $month <= 12) {
            return (int) $month;
        }

        return self::MONTHS[$month] ?? null;
    }
}
