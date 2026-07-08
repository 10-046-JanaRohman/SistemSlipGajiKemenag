<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [

        'user_id',

        'nip',
        'nip_lama',
        'nama',

        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',

        'agama',

        'status_pegawai',

        'golongan',

        'status_kawin',

        'alamat',

        'jenjang_pendidikan',

        'pendidikan',

        'status_kerja',

        'tmt_cpns',

        'tmt_pensiun',

        'usia_pensiun',

        'id_jabatan',
        'jabatan',

        'level_jabatan',

        'keterangan_satuan_kerja',

        'id_satker_1',
        'satker_1',

        'id_satker_2',
        'satker_2',

        'id_satker_3',
        'satker_3',

        'id_satker_4',
        'satker_4',

        'id_satker_5',
        'satker_5',

        'id_grup_satuan_kerja',
        'grup_satuan_kerja',

        'npwp',

        'nama_bank',

        'rekening',

        'kdbanksp',

        'nmbanksp',

        'kdpos',

        'kdnegara',

        'kdkppn',

        'no_hp',

        'extra',
    ];

    protected $casts = [

        'tanggal_lahir' => 'date',

        'tmt_cpns' => 'date',

        'tmt_pensiun' => 'date',

        'extra' => 'array',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function slipGajis()
    {
        return $this->hasMany(SlipGaji::class);
    }
}