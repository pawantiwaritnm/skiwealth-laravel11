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
        Schema::create('market_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registration')->onDelete('cascade');
            $table->tinyInteger('cash')->default(0);
            $table->tinyInteger('futures_options')->default(0);
            $table->tinyInteger('commodity')->default(0);
            $table->tinyInteger('currency')->default(0);
            $table->tinyInteger('mutual_fund')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamp('added_on')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_segments');
    }
};
