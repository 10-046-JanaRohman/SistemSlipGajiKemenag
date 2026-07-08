<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gaji_import_batches', function (Blueprint $table) {

            $table->id();

            $table->string('bulan');

            $table->year('tahun');

            $table->string('nama_file');

            $table->string('lokasi_file');

            $table->integer('jumlah_data')->default(0);

            $table->integer('berhasil')->default(0);

            $table->integer('gagal')->default(0);

            $table->foreignId('uploaded_by')
                    ->constrained('users')
                    ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gaji_import_batches');
    }
};