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
        Schema::create('sandbox_bank_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->nullable()->constrained('registration')->onDelete('set null');
            $table->string('ip', 45)->nullable();
            $table->string('api_endpoint', 255)->nullable();
            $table->text('request_payload')->nullable();
            $table->text('response_payload')->nullable();
            $table->string('status_code', 10)->nullable();
            $table->enum('verification_type', ['pan', 'bank_account', 'aadhaar', 'ifsc', 'other'])->nullable();
            $table->tinyInteger('success')->default(0)->comment('0=Failed, 1=Success');
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sandbox_bank_log');
    }
};
