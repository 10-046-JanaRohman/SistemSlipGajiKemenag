<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'nip')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nip')->nullable()->unique()->after('email');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'nip')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['nip']);
                $table->dropColumn('nip');
            });
        }
    }
};
