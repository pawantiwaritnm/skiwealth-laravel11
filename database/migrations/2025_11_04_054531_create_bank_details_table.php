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
        Schema::create('bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registration')->onDelete('cascade');
            $table->string('ifsc_code', 20);
            $table->string('account_number', 50);
            $table->string('account_type', 50)->nullable();
            $table->string('bank', 100)->nullable();
            $table->string('branch', 100)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('micr', 20)->nullable();
            $table->string('name_at_bank', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('added_on')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_details');
    }
};
