<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gaji_import_batches', function (Blueprint $table) {
            $table->unsignedInteger('ditambahkan')->default(0)->after('berhasil');
            $table->unsignedInteger('diperbarui')->default(0)->after('ditambahkan');
        });
    }

    public function down(): void
    {
        Schema::table('gaji_import_batches', function (Blueprint $table) {
            $table->dropColumn(['ditambahkan', 'diperbarui']);
        });
    }
};
