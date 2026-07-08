<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slip_gajis', function (Blueprint $table) {
            $table->foreignId('import_batch_id')
                ->nullable()
                ->after('pegawai_id')
                ->constrained('gaji_import_batches')
                ->nullOnDelete();

            $table->string('nomor_gaji')->nullable()->after('import_batch_id');
            $table->string('kdsatker')->nullable()->after('nomor_gaji');
            $table->string('kdanak')->nullable()->after('kdsatker');
            $table->string('kdjns')->nullable()->after('kdanak');

            $table->string('nip')->nullable()->after('kdjns');
            $table->string('nmpeg')->nullable()->after('nip');
            $table->string('kdduduk')->nullable()->after('nmpeg');
            $table->string('kdgol')->nullable()->after('kdduduk');

            $table->string('npwp')->nullable()->after('kdgol');
            $table->string('nmrek')->nullable()->after('npwp');
            $table->string('nm_bank')->nullable()->after('nmrek');
            $table->string('rekening')->nullable()->after('nm_bank');
            $table->string('kdbanksp')->nullable()->after('rekening');
            $table->string('nmbanksp')->nullable()->after('kdbanksp');
            $table->string('kdpos')->nullable()->after('nmbanksp');
            $table->string('kdnegara')->nullable()->after('kdpos');
            $table->string('kdkppn')->nullable()->after('kdnegara');
            $table->string('tipesup')->nullable()->after('kdkppn');

            $table->decimal('gpokok', 15, 2)->default(0)->after('tipesup');
            $table->decimal('tjistri', 15, 2)->default(0)->after('gpokok');
            $table->decimal('tjanak', 15, 2)->default(0)->after('tjistri');
            $table->decimal('tjupns', 15, 2)->default(0)->after('tjanak');
            $table->decimal('tjstruk', 15, 2)->default(0)->after('tjupns');
            $table->decimal('tjfungsi', 15, 2)->default(0)->after('tjstruk');
            $table->decimal('tjdaerah', 15, 2)->default(0)->after('tjfungsi');
            $table->decimal('tjpencil', 15, 2)->default(0)->after('tjdaerah');
            $table->decimal('tjlain', 15, 2)->default(0)->after('tjpencil');
            $table->decimal('tjkompen', 15, 2)->default(0)->after('tjlain');
            $table->decimal('pembul', 15, 2)->default(0)->after('tjkompen');
            $table->decimal('tjberas', 15, 2)->default(0)->after('pembul');
            $table->decimal('tjpph', 15, 2)->default(0)->after('tjberas');

            $table->decimal('potpfkbul', 15, 2)->default(0)->after('tjpph');
            $table->decimal('potpfk2', 15, 2)->default(0)->after('potpfkbul');
            $table->decimal('potpfk10', 15, 2)->default(0)->after('potpfk2');
            $table->decimal('potpph', 15, 2)->default(0)->after('potpfk10');
            $table->decimal('potswrum', 15, 2)->default(0)->after('potpph');
            $table->decimal('potkelbtj', 15, 2)->default(0)->after('potswrum');
            $table->decimal('potlain', 15, 2)->default(0)->after('potkelbtj');
            $table->decimal('pottabrum', 15, 2)->default(0)->after('potlain');
            $table->decimal('bpjs', 15, 2)->default(0)->after('pottabrum');
            $table->decimal('bpjs2', 15, 2)->default(0)->after('bpjs');

            $table->decimal('bersih_rinci', 15, 2)->default(0)->after('bpjs2');
            $table->string('sandi')->nullable()->after('bersih_rinci');

            $table->string('kdkawin')->nullable()->after('sandi');
            $table->string('kdjab')->nullable()->after('kdkawin');
            $table->string('thngj', 4)->nullable()->after('kdjab');
            $table->string('kdgapok')->nullable()->after('thngj');

            $table->json('extra')->nullable()->after('kdgapok');

            $table->unique(['pegawai_id', 'bulan', 'tahun'], 'slip_gajis_pegawai_bulan_tahun_unique');
        });
    }

    public function down(): void
    {
        Schema::table('slip_gajis', function (Blueprint $table) {
            $table->dropUnique('slip_gajis_pegawai_bulan_tahun_unique');
            $table->dropConstrainedForeignId('import_batch_id');

            $table->dropColumn([
                'nomor_gaji',
                'kdsatker',
                'kdanak',
                'kdjns',
                'nip',
                'nmpeg',
                'kdduduk',
                'kdgol',
                'npwp',
                'nmrek',
                'nm_bank',
                'rekening',
                'kdbanksp',
                'nmbanksp',
                'kdpos',
                'kdnegara',
                'kdkppn',
                'tipesup',
                'gpokok',
                'tjistri',
                'tjanak',
                'tjupns',
                'tjstruk',
                'tjfungsi',
                'tjdaerah',
                'tjpencil',
                'tjlain',
                'tjkompen',
                'pembul',
                'tjberas',
                'tjpph',
                'potpfkbul',
                'potpfk2',
                'potpfk10',
                'potpph',
                'potswrum',
                'potkelbtj',
                'potlain',
                'pottabrum',
                'bpjs',
                'bpjs2',
                'bersih_rinci',
                'sandi',
                'kdkawin',
                'kdjab',
                'thngj',
                'kdgapok',
                'extra',
            ]);
        });
    }
};