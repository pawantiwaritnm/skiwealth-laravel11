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
        Schema::create('personal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registration')->onDelete('cascade');
            $table->string('father_name', 100);
            $table->string('mother_name', 100);
            $table->date('dob');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('occupation', 100)->nullable();
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed'])->nullable();
            $table->string('pan_no', 10)->unique();
            $table->string('pan_name', 100)->nullable();
            $table->string('aadhaar_number', 12);
            $table->string('residential_status', 50)->nullable();
            $table->string('annual_income', 50)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('added_on')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_details');
    }
};
