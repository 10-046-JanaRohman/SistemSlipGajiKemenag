<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | IDENTITAS PEGAWAI
            |--------------------------------------------------------------------------
            */

            $table->string('nip_lama')->nullable()->after('nip');

            $table->string('tempat_lahir')->nullable();

            $table->date('tanggal_lahir')->nullable();

            $table->enum('jenis_kelamin',[
                'L',
                'P'
            ])->nullable();

            $table->string('agama')->nullable();

            $table->string('status_pegawai')->nullable();

            $table->string('status_kawin')->nullable();

            $table->text('alamat')->nullable();

            /*
            |--------------------------------------------------------------------------
            | PENDIDIKAN
            |--------------------------------------------------------------------------
            */

            $table->string('jenjang_pendidikan')->nullable();

            $table->string('pendidikan')->nullable();

            /*
            |--------------------------------------------------------------------------
            | KEPEGAWAIAN
            |--------------------------------------------------------------------------
            */

            $table->string('status_kerja')->nullable();

            $table->date('tmt_cpns')->nullable();

            $table->date('tmt_pensiun')->nullable();

            $table->integer('usia_pensiun')->nullable();

            /*
            |--------------------------------------------------------------------------
            | JABATAN
            |--------------------------------------------------------------------------
            */

            $table->string('id_jabatan')->nullable();

            $table->string('level_jabatan')->nullable();

            /*
            |--------------------------------------------------------------------------
            | SATKER
            |--------------------------------------------------------------------------
            */

            $table->text('keterangan_satuan_kerja')->nullable();

            $table->string('id_satker_1')->nullable();

            $table->string('satker_1')->nullable();

            $table->string('id_satker_2')->nullable();

            $table->string('satker_2')->nullable();

            $table->string('id_satker_3')->nullable();

            $table->string('satker_3')->nullable();

            $table->string('id_satker_4')->nullable();

            $table->string('satker_4')->nullable();

            $table->string('id_satker_5')->nullable();

            $table->string('satker_5')->nullable();

            $table->string('id_grup_satuan_kerja')->nullable();

            $table->string('grup_satuan_kerja')->nullable();

            /*
            |--------------------------------------------------------------------------
            | BANK
            |--------------------------------------------------------------------------
            */

            $table->string('npwp')->nullable();

            $table->string('rekening')->nullable();

            $table->string('nama_bank')->nullable();

            /*
            |--------------------------------------------------------------------------
            | EXTRA
            |--------------------------------------------------------------------------
            */

            $table->json('extra')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {

            $table->dropColumn([
                'nip_lama',
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'agama',
                'status_pegawai',
                'status_kawin',
                'alamat',
                'jenjang_pendidikan',
                'pendidikan',
                'status_kerja',
                'tmt_cpns',
                'tmt_pensiun',
                'usia_pensiun',
                'id_jabatan',
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
                'rekening',
                'nama_bank',
                'extra'
            ]);

        });
    }
};