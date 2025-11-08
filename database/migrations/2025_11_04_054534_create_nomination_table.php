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
        Schema::create('nomination', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registration')->onDelete('cascade');
            $table->tinyInteger('nominee_minor')->default(0)->comment('0=No, 1=Yes');
            $table->string('guardian_name', 100)->nullable();
            $table->string('guardian_mobile', 20)->nullable();
            $table->string('guardian_email', 100)->nullable();
            $table->string('relation_of_guardian', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('guardian_address', 255)->nullable();
            $table->string('guardian_city', 100)->nullable();
            $table->string('guardian_state', 100)->nullable();
            $table->string('guardian_country', 100)->nullable();
            $table->string('guardian_pin_code', 10)->nullable();
            $table->string('guardian_identification', 50)->nullable();
            $table->string('guardian_identification_value', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('added_on')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomination');
    }
};
