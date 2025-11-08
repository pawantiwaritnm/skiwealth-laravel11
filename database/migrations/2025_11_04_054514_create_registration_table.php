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
        Schema::create('registration', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('mobile', 20)->unique();
            $table->string('referral_code', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('mobile_status')->default(0);
            $table->tinyInteger('step_number')->default(1);
            $table->string('application_number', 50)->nullable()->unique();
            $table->dateTime('application_date')->nullable();
            $table->string('otp_number', 10)->nullable();
            $table->timestamp('added_on')->useCurrent();
            $table->tinyInteger('reg_flag')->default(0);
            $table->tinyInteger('kyc_uploaded')->default(0);
            $table->text('webhook_data')->nullable();
            $table->tinyInteger('webhook_status')->default(0);

            $table->index('mobile');
            $table->index('application_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration');
    }
};
