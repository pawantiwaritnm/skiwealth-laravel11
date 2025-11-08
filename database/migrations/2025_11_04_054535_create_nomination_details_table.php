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
        Schema::create('nomination_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomination_id')->constrained('nomination')->onDelete('cascade');
            $table->string('name_of_nominee', 100);
            $table->string('nominee_mobile', 20);
            $table->string('nominee_email', 100);
            $table->decimal('share_of_nominees', 5, 2)->default(0.00);
            $table->string('relation_applicant_name_nominees', 100)->nullable();
            $table->string('nominee_address', 255);
            $table->string('nominee_city', 100);
            $table->string('nominee_state', 100);
            $table->string('nominees_country', 100);
            $table->string('nominee_pin_code', 10);
            $table->string('nominee_identification', 50)->comment('photograph, pan, aadhaar, etc.');
            $table->string('nominee_document', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('added_on')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomination_details');
    }
};
