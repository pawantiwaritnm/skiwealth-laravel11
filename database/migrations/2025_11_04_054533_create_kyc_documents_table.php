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
        Schema::create('kyc_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registration')->onDelete('cascade');
            $table->string('pan_card_front', 255)->nullable();
            $table->string('pan_card_back', 255)->nullable();
            $table->string('aadhaar_front', 255)->nullable();
            $table->string('aadhaar_back', 255)->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('signature', 255)->nullable();
            $table->string('bank_proof', 255)->nullable();
            $table->string('income_proof', 255)->nullable();
            $table->string('address_proof', 255)->nullable();
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
        Schema::dropIfExists('kyc_documents');
    }
};
