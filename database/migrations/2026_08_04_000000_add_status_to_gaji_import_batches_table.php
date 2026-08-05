<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gaji_import_batches', function (Blueprint $table) {
            // Batch lama merupakan riwayat import sebelum status proses ditambahkan.
            $table->string('status', 20)->default('completed')->after('lokasi_file')->index();
        });
    }

    public function down(): void
    {
        Schema::table('gaji_import_batches', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
};
