<?php

namespace App\Services;

use App\Models\GajiImportBatch;
use App\Models\Pegawai;
use App\Models\SlipGaji;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Services\SlipGajiCalculator;

class GajiImportRowProcessor
{
    public function process(array $row, GajiImportBatch $batch): bool
    {
        $data = collect($row)
            ->mapWithKeys(fn ($value, $key) => [strtolower(trim((string) $key)) => $value])
            ->toArray();

        $nip = trim((string) ($data['nip'] ?? ''));
        $nama = trim((string) ($data['nmpeg'] ?? $data['nama'] ?? ''));

        if ($nip === '' || $nama === '') {
            return false;
        }

        DB::beginTransaction();

        try {
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

            $pegawai = Pegawai::firstOrNew(['nip' => $nip]);

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
            $pegawai->status_kawin = $data['status_kawin'] ?? $data['kdkawin'] ?? null;
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
            $pegawai->nama_bank = $data['nama_bank'] ?? $data['nm_bank'] ?? $data['nmbankspan'] ?? null;
            $pegawai->no_hp = $data['no_hp'] ?? $data['no hp'] ?? null;
            $pegawai->extra = $data;
            $pegawai->save();

            $hasil = SlipGajiCalculator::hitung($data);

            $slip = SlipGaji::where('pegawai_id', $pegawai->id)
                ->where('bulan', $batch->bulan)
                ->where('tahun', $batch->tahun)
                ->first() ?? new SlipGaji();

            $slip->pegawai_id = $pegawai->id;
            $slip->import_batch_id = $batch->id;
            $slip->bulan = $batch->bulan;
            $slip->tahun = $batch->tahun;
            $slip->tanggal_terbit = now()->toDateString();
            $slip->gaji_pokok = $hasil['gaji_pokok'];
            $slip->tunjangan = $hasil['total_pendapatan'] - $hasil['gaji_pokok'];
            $slip->potongan = $hasil['total_potongan'];
            $slip->gaji_bersih = $hasil['gaji_bersih'];
            $slip->detail_gaji = $data;
            $slip->save();

            DB::commit();

            return true;
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Import gaji gagal', [
                'nip' => $nip,
                'nama' => $nama,
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return false;
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
        } catch (\Throwable) {
            return null;
        }
    }

    private function gender($value): ?string
    {
        if (! $value) {
            return null;
        }

        $value = strtoupper(trim((string) $value));

        return match ($value) {
            'L', 'LAKI', 'LAKI-LAKI', 'LAKI LAKI', 'PRIA', 'M', 'MALE' => 'L',
            'P', 'PEREMPUAN', 'WANITA', 'F', 'FEMALE' => 'P',
            default => null,
        };
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
