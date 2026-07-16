<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('slip_gajis', function (Blueprint $table) {

            $table->id();

            $table->foreignId('pegawai_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('bulan');

            $table->year('tahun');

            $table->date('tanggal_terbit');

            $table->decimal('gaji_pokok',15,2);

            $table->decimal('tunjangan',15,2);

            $table->decimal('potongan',15,2);
            $table->decimal('gaji_bersih',15,2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slip_gajis');
    }
};
