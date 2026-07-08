<?php

namespace App\Imports;

use App\Models\GajiImportBatch;
use App\Models\Pegawai;
use App\Models\SlipGaji;
use App\Models\User;
use App\Services\SlipGajiCalculator;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Hash;

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
        $this->total = $rows->count();

        foreach ($rows as $row) {

            $data = collect($row)
                ->mapWithKeys(function ($value, $key) {
                    return [
                        strtolower(trim((string) $key)) => $value,
                    ];
                })
                ->toArray();

            $nip = trim((string) ($data['nip'] ?? ''));
            $nama = trim((string) ($data['nmpeg'] ?? ''));

            if ($nip === '' || $nama === '') {
                $this->failed++;
                continue;
            }

            DB::beginTransaction();

            try {
                /*
                |--------------------------------------------------------------------------
                | USER
                |--------------------------------------------------------------------------
                */
                $user = User::firstWhere('nip', $nip);

                if (! $user) {
                    $user = new User();
                    $user->nip = $nip;
                    $user->email = $this->generateUniqueEmail($nip);
                    $user->password = Hash::make($nip);
                }

                $user->name = $nama;
                $user->role = 'pegawai';

                if (blank($user->email)) {
                    $user->email = $this->generateUniqueEmail($nip);
                }

                if (blank($user->password)) {
                    $user->password = Hash::make($nip);
                }

                $user->save();

                /*
                |--------------------------------------------------------------------------
                | PEGAWAI
                |--------------------------------------------------------------------------
                */
                $pegawai = Pegawai::firstOrNew([
                    'nip' => $nip,
                ]);

                $pegawai->user_id = $user->id;
                $pegawai->nip = $nip;
                $pegawai->nip_lama = $data['nip_lama'] ?? null;
                $pegawai->nama = $nama;
                $pegawai->tempat_lahir = $data['tempat_lahir'] ?? null;
                $pegawai->tanggal_lahir = $this->parseDate($data['tanggal_lahir'] ?? null);
                $pegawai->jenis_kelamin = $this->gender($data['jenis_kelamin'] ?? null);
                $pegawai->agama = $data['agama'] ?? null;
                $pegawai->status_pegawai = $data['status_pegawai'] ?? 'PEGAWAI';
                $pegawai->golongan = $data['golongan'] ?? $data['kdgol'] ?? '0';
                $pegawai->status_kawin = $data['status_kawin'] ?? null;
                $pegawai->alamat = $data['alamat'] ?? null;
                $pegawai->jenjang_pendidikan = $data['jenjang_pendidikan'] ?? null;
                $pegawai->pendidikan = $data['pendidikan'] ?? null;
                $pegawai->status_kerja = $data['status_kerja'] ?? null;
                $pegawai->tmt_cpns = $this->parseDate($data['tmt_cpns'] ?? null);
                $pegawai->tmt_pensiun = $this->parseDate($data['tmt_pensiun'] ?? null);
                $pegawai->usia_pensiun = $data['usia_pensiun'] ?? null;
                $pegawai->id_jabatan = $data['id_jabatan'] ?? null;
                $pegawai->jabatan = $data['jabatan'] ?? $data['kdjab'] ?? 'BELUM ADA';
                $pegawai->level_jabatan = $data['level_jabatan'] ?? null;
                $pegawai->keterangan_satuan_kerja = $data['keterangan_satuan_kerja'] ?? null;
                $pegawai->id_satker_1 = $data['id_satker_1'] ?? null;
                $pegawai->satker_1 = $data['satker_1'] ?? null;
                $pegawai->id_satker_2 = $data['id_satker_2'] ?? null;
                $pegawai->satker_2 = $data['satker_2'] ?? null;
                $pegawai->id_satker_3 = $data['id_satker_3'] ?? null;
                $pegawai->satker_3 = $data['satker_3'] ?? null;
                $pegawai->id_satker_4 = $data['id_satker_4'] ?? null;
                $pegawai->satker_4 = $data['satker_4'] ?? null;
                $pegawai->id_satker_5 = $data['id_satker_5'] ?? null;
                $pegawai->satker_5 = $data['satker_5'] ?? null;
                $pegawai->id_grup_satuan_kerja = $data['id_grup_satuan_kerja'] ?? null;
                $pegawai->grup_satuan_kerja = $data['grup_satuan_kerja'] ?? null;
                $pegawai->unit_kerja = $data['unit_kerja']
                    ?? $data['keterangan_satuan_kerja']
                    ?? $data['grup_satuan_kerja']
                    ?? $data['satker_1']
                    ?? $data['satker_2']
                    ?? $data['satker_3']
                    ?? $data['satker_4']
                    ?? $data['satker_5']
                    ?? 'KEMENAG PROV. LAMPUNG';
                
                $pegawai->npwp = $data['npwp'] ?? null;

                $pegawai->rekening = $data['rekening'] ?? null;

                $pegawai->nama_bank =
                    $data['nama_bank']
                    ?? $data['nm_bank']
                    ?? $data['nmbankspan']
                    ?? null;

                $pegawai->no_hp =
                    $data['no_hp']
                    ?? $data['no hp']
                    ?? null;
                $pegawai->extra = $data;

                $pegawai->save();

                /*
                |--------------------------------------------------------------------------
                | HITUNG GAJI
                |--------------------------------------------------------------------------
                */
                $hasil = SlipGajiCalculator::hitung($data);

                /*
                |--------------------------------------------------------------------------
                | SIMPAN SLIP GAJI
                |--------------------------------------------------------------------------
                */
                $slip = SlipGaji::where('pegawai_id', $pegawai->id)
                    ->where('bulan', $this->batch->bulan)
                    ->where('tahun', $this->batch->tahun)
                    ->first();

                if (!$slip) {
                    $slip = new SlipGaji();
                }

                $slip->pegawai_id = $pegawai->id;
                $slip->import_batch_id = $this->batch->id;
                $slip->bulan = $this->batch->bulan;
                $slip->tahun = $this->batch->tahun;
                $slip->tanggal_terbit = now()->toDateString();
                $slip->gaji_pokok = $hasil['gaji_pokok'];
                $slip->tunjangan = $hasil['total_pendapatan'] - $hasil['gaji_pokok'];
                $slip->potongan = $hasil['total_potongan'];
                $slip->gaji_bersih = $hasil['gaji_bersih'];
                $slip->detail_gaji = $data;

                $slip->save();

                DB::commit();
                $this->success++;

            } catch (\Throwable $e) {

                DB::rollBack();

                $this->failed++;

                Log::error('Import gaji gagal', [
                    'NIP' => $nip,
                    'Nama' => $nama,
                    'message' => $e->getMessage(),
                    'class' => get_class($e),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ]);

                continue;
            }
        }
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function gender($value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = strtoupper(trim((string) $value));

        return match ($value) {
            'L', 'LAKI', 'LAKI-LAKI', 'LAKI LAKI', 'PRIA', 'M', 'MALE' => 'L',
            'P', 'PEREMPUAN', 'WANITA', 'F', 'FEMALE' => 'P',
            default => null,
        };
    }

    private function generateEmail(string $nip): string
    {
        $clean = preg_replace('/\D+/', '', $nip) ?: $nip;
        return $clean . '@kemenag.local';
    }

    private function generateUniqueEmail(string $nip): string
    {
        $base = preg_replace('/\D+/', '', $nip) ?: $nip;
        $email = $base . '@kemenag.local';
        $i = 2;

        while (User::where('email', $email)->exists()) {
            $email = $base . '-' . $i . '@kemenag.local';
            $i++;
        }

        return $email;
    }
}