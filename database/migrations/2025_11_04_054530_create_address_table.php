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
        Schema::create('address', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registration')->onDelete('cascade');
            $table->text('permanent_address')->nullable();
            $table->string('permanent_address1', 255)->nullable();
            $table->string('permanent_address2', 255)->nullable();
            $table->string('permanent_address_city', 100)->nullable();
            $table->string('permanent_address_country', 100)->nullable();
            $table->string('permanent_address_pincode', 10)->nullable();
            $table->tinyInteger('is_same')->default(0)->comment('1 if correspondence same as permanent');
            $table->text('correspondence_address')->nullable();
            $table->string('correspondence_address1', 255)->nullable();
            $table->string('correspondence_address2', 255)->nullable();
            $table->string('correspondence_address_city', 100)->nullable();
            $table->string('correspondence_address_country', 100)->nullable();
            $table->string('correspondence_address_pincode', 10)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('added_on')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address');
    }
};
