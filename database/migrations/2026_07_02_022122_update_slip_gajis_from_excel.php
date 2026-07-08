<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slip_gajis', function (Blueprint $table) {

            if (!Schema::hasColumn('slip_gajis', 'import_batch_id')) {

                $table->foreignId('import_batch_id')
                    ->nullable()
                    ->after('pegawai_id')
                    ->constrained('gaji_import_batches')
                    ->nullOnDelete();

            }

        });
    }

    public function down(): void
    {
        Schema::table('slip_gajis', function (Blueprint $table) {

            if (Schema::hasColumn('slip_gajis', 'import_batch_id')) {

                $table->dropConstrainedForeignId('import_batch_id');

            }

        });
    }
};