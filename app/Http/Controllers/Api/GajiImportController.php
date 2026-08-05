<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessGajiImportJob;
use App\Models\GajiImportBatch;
use App\Models\SlipGaji;
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

    /**
     * Memberi informasi aman sebelum admin mengimpor ulang periode yang sama.
     * Tidak mengubah data apa pun.
     */
    public function periodStatus(Request $request)
    {
        if (! in_array($request->user()?->role, ['admin', 'super_admin'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk memeriksa periode import.',
            ], 403);
        }

        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'digits:4'],
        ], [
            'bulan.required' => 'Periode tidak valid. Silakan pilih bulan.',
            'bulan.between' => 'Periode tidak valid. Bulan harus antara 1 sampai 12.',
            'tahun.required' => 'Periode tidak valid. Silakan isi tahun.',
            'tahun.digits' => 'Periode tidak valid. Tahun harus terdiri dari 4 angka.',
        ]);

        // Utamakan impor yang benar-benar menghasilkan slip. Riwayat impor gagal
        // tidak boleh membuat admin mengira data periode sudah tersimpan.
        $lastImport = GajiImportBatch::with('uploader:id,name')
            ->where('bulan', $validated['bulan'])
            ->where('tahun', $validated['tahun'])
            ->where('berhasil', '>', 0)
            ->latest()
            ->first();

        $slipCount = SlipGaji::query()
            ->where('bulan', $validated['bulan'])
            ->where('tahun', $validated['tahun'])
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Status periode berhasil diperiksa.',
            'data' => [
                'has_existing_slips' => $slipCount > 0,
                'slip_count' => $slipCount,
                'last_import' => $lastImport,
            ],
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
            // Validasi berdasarkan ekstensi. Sebagian file XLS lama tidak dikenali MIME-nya
            // oleh PHP meskipun isinya valid dan bisa dibaca PhpSpreadsheet.
            'file_excel' => ['required', 'file', 'extensions:xlsx,xls,csv', 'max:20480'],
        ]);

        // Endpoint impor langsung juga menghormati kunci review agar tidak ada
        // proses lama yang menimpa data ketika sebuah periode sedang diperiksa.
        $activeReview = $this->activeReviewForPeriod((int) $validated['bulan'], (int) $validated['tahun']);
        if ($activeReview) {
            return response()->json([
                'success' => false,
                'message' => 'Periode '.$this->periodLabel($validated['bulan'], $validated['tahun'])
                    .' sedang direview oleh '.($activeReview['draft']['created_by_name'] ?? 'admin lain').'.',
            ], 409);
        }

        $file = $request->file('file_excel');
        $path = $file->store('imports/gaji', 'public');

        $batch = GajiImportBatch::create([
            'uploaded_by' => $request->user()->id,
            'bulan' => $validated['bulan'],
            'tahun' => $validated['tahun'],
            'nama_file' => $file->getClientOriginalName(),
            'lokasi_file' => $path,
            'status' => 'queued',
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

    /**
     * Dipanggil frontend saat menunggu queue agar hasil import muncul tanpa refresh halaman.
     */
    public function batchStatus(Request $request, GajiImportBatch $batch)
    {
        if (! in_array($request->user()?->role, ['admin', 'super_admin'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melihat status import.',
            ], 403);
        }

        abort_unless(
            $request->user()->role === 'super_admin' || $batch->uploaded_by === $request->user()->id,
            403
        );

        return response()->json([
            'success' => true,
            'data' => $batch->fresh(),
        ]);
    }

    public function activeReviews(Request $request)
    {
        if ($request->user()?->role !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Super Admin yang dapat melihat review aktif seluruh admin.',
            ], 403);
        }

        $reviews = collect(Storage::disk('local')->files('import-reviews'))
            ->filter(fn ($path) => str_ends_with($path, '.json'))
            ->map(fn ($path) => $this->reviewMetadata(
                basename($path, '.json'),
                json_decode(Storage::disk('local')->get($path), true) ?: []
            ))
            ->sortByDesc('created_at')
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Review aktif berhasil diambil.',
            'data' => $reviews,
        ]);
    }

    /** Status kunci review untuk satu periode, dapat dilihat semua admin. */
    public function reviewStatus(Request $request)
    {
        if (! in_array($request->user()?->role, ['admin', 'super_admin'], true)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk memeriksa review.'], 403);
        }

        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'digits:4'],
        ]);

        $active = $this->activeReviewForPeriod((int) $validated['bulan'], (int) $validated['tahun']);

        return response()->json([
            'success' => true,
            'data' => [
                'active' => $active !== null,
                'review' => $active ? $this->reviewMetadata($active['token'], $active['draft']) : null,
                'is_owner' => $active && (int) ($active['draft']['created_by'] ?? 0) === (int) $request->user()->id,
                'can_take_over' => $active && $request->user()->role === 'super_admin'
                    && (int) ($active['draft']['created_by'] ?? 0) !== (int) $request->user()->id,
            ],
        ]);
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
            'file_excel' => ['nullable', 'file', 'extensions:xlsx,xls,csv', 'max:20480'],
            'review_token' => ['nullable', 'string'],
            'page' => ['nullable', 'integer', 'min:1'],
            'bulan' => ['nullable', 'integer', 'between:1,12'],
            'tahun' => ['nullable', 'digits:4'],
        ]);

        $reviewToken = $request->input('review_token');
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 200;
        $rows = [];
        $headers = [];

        if ($request->filled('review_token')) {
            [, $draft] = $this->authorizedReviewDraft($request, $request->review_token);
            $headers = $draft['headers'] ?? [];
            $rows = $draft['rows'] ?? [];
        } else {
            if (! $request->hasFile('file_excel')) {
                return response()->json([
                    'success' => false,
                    'message' => 'File Excel wajib dipilih.',
                ], 422);
            }

            try {
                $reader = IOFactory::createReaderForFile($request->file('file_excel')->getRealPath());
                $reader->setReadDataOnly(true);
                $worksheet = $reader->load($request->file('file_excel')->getRealPath())->getActiveSheet();
            } catch (\Throwable $exception) {
                report($exception);

                return response()->json([
                    'success' => false,
                    'message' => 'File tidak dapat dibaca. Pastikan file .xls, .xlsx, atau .csv tidak rusak dan ekstensinya sesuai.',
                ], 422);
            }

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

            $period = $request->validate([
                'bulan' => ['required', 'integer', 'between:1,12'],
                'tahun' => ['required', 'digits:4'],
            ]);

            $active = $this->activeReviewForPeriod((int) $period['bulan'], (int) $period['tahun']);
            if ($active) {
                $owner = $active['draft']['created_by_name'] ?? 'admin lain';
                return response()->json([
                    'success' => false,
                    'message' => 'Periode '. $this->periodLabel($period['bulan'], $period['tahun'])
                        .' sedang direview oleh '.$owner.'. Selesaikan atau batalkan review tersebut terlebih dahulu.',
                    'data' => ['review' => $this->reviewMetadata($active['token'], $active['draft'])],
                ], 409);
            }

            $reviewToken = (string) Str::uuid();
            $draft = [
                'nama_file' => $request->file('file_excel')->getClientOriginalName(),
                'created_by' => $request->user()->id,
                'created_by_name' => $request->user()->name,
                'created_at' => now()->toIso8601String(),
                'bulan' => (int) $period['bulan'],
                'tahun' => (int) $period['tahun'],
                'headers' => array_values(array_filter($headers)),
                'rows' => $this->refreshReviewRows($rows),
            ];
            Storage::disk('local')->put(
                "import-reviews/{$reviewToken}.json",
                json_encode($draft, JSON_UNESCAPED_UNICODE)
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
                'bulan' => $draft['bulan'] ?? null,
                'tahun' => $draft['tahun'] ?? null,
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

        [$path, $draft] = $this->authorizedReviewDraft($request, $reviewToken);
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
                ...$draft,
                'nama_file' => $draft['nama_file'] ?? null,
                'headers' => $draft['headers'] ?? [],
                'rows' => $draftRows->values()->all(),
            ], JSON_UNESCAPED_UNICODE)
        );

        if ($draftRows->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data review untuk diimport.',
            ], 422);
        }

        $batch = GajiImportBatch::create([
            'uploaded_by' => $request->user()->id,
            'bulan' => $validated['bulan'],
            'tahun' => $validated['tahun'],
            'nama_file' => $draft['nama_file'] ?? 'Review Excel Manual',
            'lokasi_file' => "review://{$reviewToken}",
            'status' => 'queued',
            'jumlah_data' => $draftRows->count(),
            'berhasil' => 0,
            'gagal' => $invalidRows->count(),
            'log_gagal' => $invalidRows->map(fn ($row) => [
                'baris' => $row['row_number'] ?? null,
                'keterangan' => implode(', ', $row['errors'] ?? ['Data tidak valid']),
            ])->values()->all(),
        ]);

        \App\Jobs\ProcessReviewedGajiImportJob::dispatch($batch->id, $reviewToken);

        return response()->json([
            'success' => true,
            'message' => $invalidRows->isNotEmpty()
                ? 'Import masuk antrean. '.$invalidRows->count().' baris tidak valid dilewati dan keterangannya disimpan di riwayat import.'
                : 'Import review masuk antrean dan akan diproses di belakang layar.',
            'data' => $batch->fresh(),
        ], 202);
    }

    public function cancelReview(Request $request)
    {
        if (! in_array($request->user()?->role, ['admin', 'super_admin'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk membatalkan review import gaji.',
            ], 403);
        }

        $validated = $request->validate([
            'review_token' => ['required', 'string'],
        ]);

        [$path] = $this->authorizedReviewDraft($request, $validated['review_token']);
        Storage::disk('local')->delete($path);

        return response()->json([
            'success' => true,
            'message' => 'Review Excel dibatalkan.',
        ]);
    }

    /** Super Admin mengambil alih review agar pemilik lama tidak dapat melanjutkannya. */
    public function takeOverReview(Request $request, string $reviewToken)
    {
        if ($request->user()?->role !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'Hanya Super Admin yang dapat mengambil alih review.'], 403);
        }

        [$path, $draft] = $this->authorizedReviewDraft($request, $reviewToken);
        $previousOwner = $draft['created_by_name'] ?? 'Admin tidak diketahui';

        $draft['taken_over_from_name'] = $previousOwner;
        $draft['taken_over_at'] = now()->toIso8601String();
        $draft['created_by'] = $request->user()->id;
        $draft['created_by_name'] = $request->user()->name;
        Storage::disk('local')->put($path, json_encode($draft, JSON_UNESCAPED_UNICODE));

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil diambil alih dari '.$previousOwner.'.',
            'data' => $this->reviewMetadata($reviewToken, $draft),
        ]);
    }

    private function normalizeHeader($value): string
    {
        $value = strtolower(trim((string) $value));
        $value = preg_replace('/[^a-z0-9]+/i', '_', $value);

        return trim($value ?? '', '_');
    }

    /**
     * Draft review hanya dapat diakses pembuatnya. Super admin memiliki akses
     * penuh untuk membantu atau menyelesaikan review admin lain.
     */
    private function authorizedReviewDraft(Request $request, string $reviewToken): array
    {
        $path = "import-reviews/{$reviewToken}.json";

        if (! Storage::disk('local')->exists($path)) {
            abort(422, 'Data review Excel tidak ditemukan. Silakan upload ulang file.');
        }

        $draft = json_decode(Storage::disk('local')->get($path), true) ?: [];
        $user = $request->user();

        // Draft lama belum memiliki pemilik. Klaim oleh akun pertama yang
        // melanjutkannya supaya setelah ini tetap terlindungi per akun.
        if (empty($draft['created_by'])) {
            $draft['created_by'] = $user->id;
            $draft['created_by_name'] = $user->name;
            $draft['created_at'] = $draft['created_at'] ?? now()->toIso8601String();
            Storage::disk('local')->put($path, json_encode($draft, JSON_UNESCAPED_UNICODE));
        }

        if ($user->role !== 'super_admin' && (int) $draft['created_by'] !== (int) $user->id) {
            abort(403, 'Review Excel ini dibuat oleh admin lain dan tidak dapat Anda lanjutkan atau batalkan.');
        }

        return [$path, $draft];
    }

    private function activeReviewForPeriod(int $bulan, int $tahun): ?array
    {
        foreach (Storage::disk('local')->files('import-reviews') as $path) {
            if (! str_ends_with($path, '.json')) {
                continue;
            }

            $draft = json_decode(Storage::disk('local')->get($path), true) ?: [];
            if ((int) ($draft['bulan'] ?? 0) === $bulan && (int) ($draft['tahun'] ?? 0) === $tahun) {
                return ['token' => basename($path, '.json'), 'draft' => $draft];
            }
        }

        return null;
    }

    private function reviewMetadata(string $reviewToken, array $draft): array
    {
        return [
            'review_token' => $reviewToken,
            'nama_file' => $draft['nama_file'] ?? 'File tidak diketahui',
            'created_by' => $draft['created_by'] ?? null,
            'created_by_name' => $draft['created_by_name'] ?? 'Belum diketahui',
            'created_at' => $draft['created_at'] ?? null,
            'bulan' => $draft['bulan'] ?? null,
            'tahun' => $draft['tahun'] ?? null,
            'total_baris' => count($draft['rows'] ?? []),
        ];
    }

    private function periodLabel(int $bulan, int $tahun): string
    {
        $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return ($months[$bulan] ?? 'Periode').' '.$tahun;
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

        // Template gaji lama sering memakai tanda '-' untuk nominal nol.
        if (in_array(trim((string) $value), ['-', '—'], true)) {
            return 0.0;
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
