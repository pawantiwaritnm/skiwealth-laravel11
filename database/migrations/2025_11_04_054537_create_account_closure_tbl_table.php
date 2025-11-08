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
        Schema::create('account_closure_tbl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registration')->onDelete('cascade');
            $table->string('name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('dp_id', 50)->nullable();
            $table->string('client_master_file', 255)->nullable();
            $table->text('reason_for_closure')->nullable();
            $table->string('mobile_number', 20)->nullable();
            $table->string('target_dp_id', 50)->nullable();
            $table->string('client_id', 50)->nullable();
            $table->string('trading_code', 50)->nullable();
            $table->string('ip', 45)->nullable();
            $table->tinyInteger('verify_otp')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamp('added_on')->useCurrent();
            $table->timestamp('updated_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_closure_tbl');
    }
};
