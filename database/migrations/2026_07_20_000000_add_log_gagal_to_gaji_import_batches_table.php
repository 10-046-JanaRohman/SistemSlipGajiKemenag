<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gaji_import_batches', function (Blueprint $table) {
            $table->json('log_gagal')->nullable()->after('gagal');
        });
    }

    public function down(): void
    {
        Schema::table('gaji_import_batches', function (Blueprint $table) {
            $table->dropColumn('log_gagal');
        });
    }
};
