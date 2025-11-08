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
        Schema::create('user_capture_video', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registration')->onDelete('cascade');
            $table->string('user_video', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('ipv_otp', 10)->nullable();
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
        Schema::dropIfExists('user_capture_video');
    }
};
