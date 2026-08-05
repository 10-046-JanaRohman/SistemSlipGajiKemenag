<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slip_gaji_signature_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slip_gaji_id')->constrained('slip_gajis')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending')->index();
            $table->text('request_message');
            $table->text('admin_response')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['slip_gaji_id', 'user_id', 'status'], 'slip_ttd_request_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slip_gaji_signature_requests');
    }
};
