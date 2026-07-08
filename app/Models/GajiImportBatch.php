<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiImportBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'uploaded_by',
        'bulan',
        'tahun',
        'nama_file',
        'lokasi_file',
        'jumlah_data',
        'berhasil',
        'gagal',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function slipGajis()
    {
        return $this->hasMany(SlipGaji::class, 'import_batch_id');
    }
}