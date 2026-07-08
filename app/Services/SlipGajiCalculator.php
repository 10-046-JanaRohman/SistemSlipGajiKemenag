<?php

namespace App\Services;

class SlipGajiCalculator
{
    public static function hitung(array $data): array
    {
        /*
        |--------------------------------------------------------------------------
        | DATA PEGAWAI
        |--------------------------------------------------------------------------
        */

        $pegawai = [

            'nama'       => $data['nmpeg'] ?? '',
            'nip'        => $data['nip'] ?? '',
            'golongan'   => $data['kdgol'] ?? '',
            'jabatan'    => $data['kdjab'] ?? '',
            'unit_kerja' => 'KEMENAG PROV. LAMPUNG',
            'bank'       => $data['nmbankspan'] ?? $data['nm_bank'] ?? '',
            'rekening'   => $data['rekening'] ?? '',
            'satker'     => $data['kdsatker'] ?? '',

        ];

        /*
        |--------------------------------------------------------------------------
        | PENDAPATAN
        |--------------------------------------------------------------------------
        */

        $pendapatan = [

            'Gaji Pokok'               => self::num($data['gjpokok'] ?? 0),

            'Tunjangan Istri'          => self::num($data['tjistri'] ?? 0),

            'Tunjangan Anak'           => self::num($data['tjanak'] ?? 0),

            'Tunjangan Beras'          => self::num($data['tjberas'] ?? 0),

            'Tunjangan Jabatan'        => self::num($data['tjjabatan'] ?? 0),

            'Tunjangan Fungsional'     => self::num($data['tjfungs'] ?? 0),

            'Tunjangan Struktural'     => self::num($data['tjstruk'] ?? 0),

            'Tunjangan Umum'           => self::num($data['tjupns'] ?? 0),

            'Tunjangan Kompensasi'     => self::num($data['tjkompen'] ?? 0),

            'Tunjangan Daerah'         => self::num($data['tjdaerah'] ?? 0),

            'Tunjangan Pencilan'       => self::num($data['tjpencil'] ?? 0),

            'Tunjangan Lainnya'        => self::num($data['tjlain'] ?? 0),

            'Pembulatan'               => self::num($data['pembul'] ?? 0),

        ];

        /*
        |--------------------------------------------------------------------------
        | POTONGAN
        |--------------------------------------------------------------------------
        */

        $potongan = [

            'PPh Pasal 21'                 => self::num($data['potpph'] ?? 0),

            'Potongan FKP Bulanan'         => self::num($data['potpfkbul'] ?? 0),

            'Potongan FKP II'              => self::num($data['potpfk2'] ?? 0),

            'Potongan FKP X'               => self::num($data['potpfk10'] ?? 0),

            'Potongan SWRUM'               => self::num($data['potswrum'] ?? 0),

            'Potongan Kelebihan Tunjangan' => self::num($data['potkelbtj'] ?? 0),

            'Potongan Lainnya'             => self::num($data['potlain'] ?? 0),

            'Tabungan Perumahan'           => self::num($data['pottabrum'] ?? 0),

            'BPJS'                         => self::num($data['bpjs'] ?? 0),

            'BPJS 2'                       => self::num($data['bpjs2'] ?? 0),

        ];

        /*
        |--------------------------------------------------------------------------
        | HAPUS NILAI NOL AGAR RAPI
        |--------------------------------------------------------------------------
        */

        $pendapatan = array_filter(
            $pendapatan,
            fn($v) => $v > 0
        );

        $potongan = array_filter(
            $potongan,
            fn($v) => $v > 0
        );

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        $totalPendapatan = array_sum($pendapatan);

        $totalPotongan = array_sum($potongan);

        /**
         * Gaji Pokok (nilai asli dari excel sebelum difilter)
         */
        $gajiPokok = self::num($data['gjpokok'] ?? 0);

        /*
        |--------------------------------------------------------------------------
        | GAJI BERSIH RESMI DARI EXCEL
        |--------------------------------------------------------------------------
        */

        // Jika ada nilai bersih dari Excel dan tidak 0, gunakan itu
        // Jika tidak, hitung dari total pendapatan - total potongan
        $gajiBersih = self::num(
            (!empty($data['bersih']) && $data['bersih'] > 0)
                ? $data['bersih']
                : ($totalPendapatan - $totalPotongan)
        );

        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return [

            'pegawai' => $pegawai,

            'pendapatan' => $pendapatan,

            'potongan' => $potongan,

            'total_pendapatan' => $totalPendapatan,

            'total_potongan' => $totalPotongan,

            'gaji_pokok' => $gajiPokok,

            'gaji_bersih' => $gajiBersih,

            'bulan' => $data['bulan'] ?? '',

            'tahun' => $data['tahun'] ?? '',

        ];
    }

    private static function num($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = str_replace(
            ['Rp', 'rp', ' '],
            '',
            (string) $value
        );

        $value = str_replace('.', '', $value);

        $value = str_replace(',', '.', $value);

        return is_numeric($value)
            ? (float) $value
            : 0;
    }
}