<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlipGaji extends Model
{
    use HasFactory;

    protected $fillable = [

        'pegawai_id',

        'import_batch_id',

        'bulan',

        'tahun',

        'tanggal_terbit',

        'gaji_pokok',

        'tunjangan',

        'potongan',

        'gaji_bersih',

        'detail_gaji',

    ];

    protected $casts = [

        'tanggal_terbit' => 'date',

        'gaji_pokok' => 'decimal:2',

        'tunjangan' => 'decimal:2',

        'potongan' => 'decimal:2',

        'gaji_bersih' => 'decimal:2',

        'detail_gaji' => 'array',

    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function importBatch()
    {
        return $this->belongsTo(
            GajiImportBatch::class,
            'import_batch_id'
        );
    }
}