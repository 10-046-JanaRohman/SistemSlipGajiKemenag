<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessGajiImportJob;
use App\Models\GajiImportBatch;
use App\Services\GajiImportRowProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GajiImportController extends Controller
{
    public function index(Request $request)
    {
        if (! in_array($request->user()?->role, ['admin', 'super_admin'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke riwayat import.',
            ], 403);
        }

        $batches = GajiImportBatch::with('uploader:id,name')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat import berhasil diambil.',
            'data' => $batches,
        ]);
    }

    public function store(Request $request)
    {
        if (! in_array($request->user()?->role, ['admin', 'super_admin'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk import gaji.',
            ], 403);
        }

        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'digits:4'],
            'file_excel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $file = $request->file('file_excel');
        $path = $file->store('imports/gaji', 'public');

        $batch = GajiImportBatch::create([
            'uploaded_by' => $request->user()->id,
            'bulan' => $validated['bulan'],
            'tahun' => $validated['tahun'],
            'nama_file' => $file->getClientOriginalName(),
            'lokasi_file' => $path,
            'jumlah_data' => 0,
            'berhasil' => 0,
            'gagal' => 0,
        ]);

        ProcessGajiImportJob::dispatch($batch->id, $path);

        return response()->json([
            'success' => true,
            'message' => 'File masuk antrean import.',
            'data' => $batch,
        ], 202);
    }

    public function preview(Request $request)
    {
        if ($request->user()?->role !== 'admin' && $request->user()?->role !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk review import gaji.',
            ], 403);
        }

        $request->validate([
            'file_excel' => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'review_token' => ['nullable', 'string'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $reviewToken = $request->input('review_token');
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 200;
        $rows = [];
        $headers = [];

        if ($request->filled('review_token')) {
            $path = "import-reviews/{$request->review_token}.json";

            if (! Storage::disk('local')->exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data review Excel tidak ditemukan. Silakan upload ulang file.',
                ], 422);
            }

            $draft = json_decode(Storage::disk('local')->get($path), true) ?: [];
            $headers = $draft['headers'] ?? [];
            $rows = $draft['rows'] ?? [];
        } else {
            if (! $request->hasFile('file_excel')) {
                return response()->json([
                    'success' => false,
                    'message' => 'File Excel wajib dipilih.',
                ], 422);
            }

            $reader = IOFactory::createReaderForFile($request->file('file_excel')->getRealPath());
            $reader->setReadDataOnly(true);
            $worksheet = $reader->load($request->file('file_excel')->getRealPath())->getActiveSheet();

            $headerRow = null;

            foreach ($worksheet->getRowIterator() as $row) {
                $index = $row->getRowIndex();
                $values = $this->rowValues($row);
                $nonEmpty = array_filter($values, fn ($value) => $value !== null && trim((string) $value) !== '');

                if (count($nonEmpty) >= 2) {
                    $headerRow = $index;
                    $headers = array_map(fn ($value) => $this->normalizeHeader($value), $values);
                    break;
                }
            }

            if (! $headerRow) {
                return response()->json([
                    'success' => false,
                    'message' => 'Header Excel tidak ditemukan.',
                ], 422);
            }

            foreach ($worksheet->getRowIterator() as $row) {
                $index = $row->getRowIndex();

                if ($index <= $headerRow) {
                    continue;
                }

                $values = $this->rowValues($row);
                $data = [];

                foreach ($headers as $columnIndex => $header) {
                    if ($header === '') {
                        continue;
                    }

                    $data[$header] = $values[$columnIndex] ?? null;
                }

                $hasValue = collect($data)->contains(fn ($value) => $value !== null && trim((string) $value) !== '');

                if (! $hasValue) {
                    continue;
                }

                $errors = $this->reviewErrors($data);

                $rows[] = [
                    'row_number' => $index,
                    'valid' => count($errors) === 0,
                    'errors' => $errors,
                    'data' => $data,
                ];
            }

            $reviewToken = (string) Str::uuid();
            Storage::disk('local')->put(
                "import-reviews/{$reviewToken}.json",
                json_encode([
                    'headers' => array_values(array_filter($headers)),
                    'rows' => $this->refreshReviewRows($rows),
                ], JSON_UNESCAPED_UNICODE)
            );

            $request->merge(['review_token' => $reviewToken]);
        }

        if (empty($headers) || empty($rows)) {
            return response()->json([
                'success' => false,
                'message' => 'Data review Excel tidak ditemukan.',
            ], 422);
        }

        $rows = $this->refreshReviewRows($rows);

        $previewRows = collect($rows)
            ->sortBy(fn ($row) => $row['valid'] ? 1 : 0)
            ->forPage($page, $perPage)
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'message' => 'Preview Excel berhasil dibuat.',
            'data' => [
                'review_token' => $reviewToken,
                'headers' => array_values(array_filter($headers)),
                'rows' => $previewRows,
                'total' => count($rows),
                'valid' => collect($rows)->where('valid', true)->count(),
                'invalid' => collect($rows)->where('valid', false)->count(),
                'preview_limit' => $perPage,
                'preview_page' => $page,
                'has_more' => ($page * $perPage) < count($rows),
            ],
        ]);
    }

    public function importReviewed(Request $request, GajiImportRowProcessor $processor)
    {
        if ($request->user()?->role !== 'admin' && $request->user()?->role !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk import gaji.',
            ], 403);
        }

        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'digits:4'],
            'review_token' => ['nullable', 'string'],
            'rows' => ['nullable', 'array'],
            'rows.*.row_number' => ['nullable', 'integer'],
            'rows.*.data' => ['nullable', 'array'],
        ]);

        $reviewToken = $validated['review_token'] ?? null;
        $editedRows = collect($validated['rows'] ?? [])->filter(fn ($row) => ! empty($row['row_number']))->values();

        if (blank($reviewToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Token review tidak ditemukan. Silakan review ulang file Excel.',
            ], 422);
        }

        $path = "import-reviews/{$reviewToken}.json";

        if (! Storage::disk('local')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Data review Excel tidak ditemukan. Silakan review ulang file Excel.',
            ], 422);
        }

        $draft = json_decode(Storage::disk('local')->get($path), true) ?: [];
        $draftRows = collect($draft['rows'] ?? []);

        if ($editedRows->isNotEmpty()) {
            $editedMap = $editedRows->keyBy(fn ($row) => (string) ($row['row_number'] ?? ''));

            $draftRows = $draftRows->map(function ($row) use ($editedMap) {
                $rowNumber = (string) ($row['row_number'] ?? '');
                $edited = $editedMap->get($rowNumber);

                if ($edited && isset($edited['data']) && is_array($edited['data'])) {
                    $row['data'] = $edited['data'];
                }

                return $row;
            })->values();
        }

        $draftRows = collect($this->refreshReviewRows($draftRows->values()->all()));
        $invalidRows = $draftRows->filter(fn ($row) => ! ($row['valid'] ?? false));

        Storage::disk('local')->put(
            $path,
            json_encode([
                'headers' => $draft['headers'] ?? [],
                'rows' => $draftRows->values()->all(),
            ], JSON_UNESCAPED_UNICODE)
        );

        if ($invalidRows->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Masih ada '.$invalidRows->count().' baris yang perlu diperbaiki sebelum import.',
                'data' => [
                    'invalid' => $invalidRows->count(),
                    'first_invalid_rows' => $invalidRows->take(10)->values()->all(),
                ],
            ], 422);
        }

        $rows = $draftRows->map(fn ($row) => $row['data'] ?? [])->values()->all();

        if (count($rows) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data review untuk diimport.',
            ], 422);
        }

        $batch = GajiImportBatch::create([
            'uploaded_by' => $request->user()->id,
            'bulan' => $validated['bulan'],
            'tahun' => $validated['tahun'],
            'nama_file' => 'Review Excel Manual',
            'lokasi_file' => 'review://manual',
            'jumlah_data' => count($rows),
            'berhasil' => 0,
            'gagal' => 0,
        ]);

        \App\Jobs\ProcessReviewedGajiImportJob::dispatch($batch->id, $reviewToken);

        return response()->json([
            'success' => true,
            'message' => 'Import review masuk antrean dan akan diproses di belakang layar.',
            'data' => $batch->fresh(),
        ], 202);
    }

    private function normalizeHeader($value): string
    {
        $value = strtolower(trim((string) $value));
        $value = preg_replace('/[^a-z0-9]+/i', '_', $value);

        return trim($value ?? '', '_');
    }

    private function rowValues($row): array
    {
        $values = [];
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        foreach ($cellIterator as $cell) {
            $columnIndex = Coordinate::columnIndexFromString($cell->getColumn()) - 1;
            $values[$columnIndex] = $cell->getValue();
        }

        return $values;
    }

    private function refreshReviewRows(array $rows): array
    {
        return collect($rows)->map(function ($row) {
            $data = $row['data'] ?? [];
            $errors = $this->reviewErrors(is_array($data) ? $data : []);

            return [
                ...$row,
                'valid' => count($errors) === 0,
                'errors' => $errors,
            ];
        })->values()->all();
    }

    private function reviewErrors(array $data): array
    {
        $errors = [];

        if (blank($data['nip'] ?? null)) {
            $errors[] = 'NIP kosong';
        }

        if (blank($data['nmpeg'] ?? $data['nama'] ?? null)) {
            $errors[] = 'Nama pegawai kosong';
        }

        if (isset($data['thngj']) && ! blank($data['thngj'] ?? null)) {
            $year = (int) $this->normalizedNumber($data['thngj']);

            if ($year < 2000 || $year > 2100) {
                $errors[] = 'THNGJ tidak valid';
            }
        }

        foreach ($this->numericReviewColumns() as $column) {
            if (! array_key_exists($column, $data) || blank($data[$column])) {
                continue;
            }

            if ($this->normalizedNumber($data[$column]) === null) {
                $errors[] = strtoupper($column).' harus angka';
            }
        }

        return $errors;
    }

    private function numericReviewColumns(): array
    {
        return [
            'bulan', 'tahun', 'nogaji', 'kdjns', 'kdgol', 'gjpokok', 'tjistri',
            'tjanak', 'tjupns', 'tjstruk', 'tjfungs', 'tjdaerah', 'tjpencil',
            'tjlain', 'tjkompen', 'pembul', 'tjberas', 'tjpph', 'potpfkbul',
            'potpfk2', 'potpfk10', 'potpph', 'potswrum', 'potkelbtj', 'potlain',
            'pottabrum', 'bersih', 'kdkawin', 'kdjab', 'thngj',
            'bpjs', 'bpjs2',
        ];
    }

    private function normalizedNumber($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = str_replace(['Rp', 'rp', ' '], '', (string) $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : null;
    }
}
