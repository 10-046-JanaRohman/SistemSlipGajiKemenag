<?php

namespace App\Services;

class SlipGajiFormatter
{
    public static function format(array $detail): array
    {
        $pendapatan = [

            'Gaji Pokok'            => self::num($detail['gjpokok'] ?? 0),
            'Tunjangan Istri'       => self::num($detail['tjistri'] ?? 0),
            'Tunjangan Anak'        => self::num($detail['tjanak'] ?? 0),
            'Tunjangan Beras'       => self::num($detail['tjberas'] ?? 0),
            'Tunjangan Fungsional'  => self::num($detail['tjfungs'] ?? 0),
            'Tunjangan Struktural'  => self::num($detail['tjstruk'] ?? 0),
            'Tunjangan Umum'        => self::num($detail['tjupns'] ?? 0),
            'Tunjangan Kompensasi'  => self::num($detail['tjkompen'] ?? 0),
            'Tunjangan Daerah'      => self::num($detail['tjdaerah'] ?? 0),
            'Tunjangan Lain'        => self::num($detail['tjlain'] ?? 0),
            'Pembulatan'            => self::num($detail['pembul'] ?? 0),

        ];

        $potongan = [

            'PPh'                   => self::num($detail['potpph'] ?? 0),
            'Potongan FKP Bulanan'  => self::num($detail['potpfkbul'] ?? 0),
            'Potongan FKP II'       => self::num($detail['potpfk2'] ?? 0),
            'Potongan FKP X'        => self::num($detail['potpfk10'] ?? 0),
            'Potongan SWRUM'        => self::num($detail['potswrum'] ?? 0),
            'Potongan Kelebihan TJ' => self::num($detail['potkelbtj'] ?? 0),
            'Potongan Lain'         => self::num($detail['potlain'] ?? 0),
            'Tabungan Perumahan'    => self::num($detail['pottabrum'] ?? 0),

            // TAMBAHAN
            'BPJS'                  => self::num($detail['bpjs'] ?? 0),
            'BPJS 2'                => self::num($detail['bpjs2'] ?? 0),

        ];

        $pendapatan = array_filter(
            $pendapatan,
            fn($nilai) => $nilai > 0
        );

        $potongan = array_filter(
            $potongan,
            fn($nilai) => $nilai > 0
        );

        return [

            'pegawai' => [

                'nama'      => $detail['nmpeg'] ?? '',
                'nip'       => $detail['nip'] ?? '',
                'golongan'  => $detail['kdgol'] ?? '',
                'jabatan'   => $detail['kdjab'] ?? '',
                'bank'      => $detail['nmbankspan'] ?? '',
                'rekening'  => $detail['rekening'] ?? '',

            ],

            'pendapatan' => $pendapatan,

            'potongan' => $potongan,

            'total_pendapatan' => array_sum($pendapatan),

            'total_potongan' => array_sum($potongan),

            'gaji_bersih' => self::num($detail['bersih'] ?? 0),

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

        $value = str_replace(['Rp', 'rp', ' '], '', (string) $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : 0;
    }
}