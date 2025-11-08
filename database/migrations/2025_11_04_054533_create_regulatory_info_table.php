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
        Schema::create('regulatory_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registration')->onDelete('cascade');
            $table->string('number_of_years_of_investment', 50)->nullable();
            $table->enum('pep', ['Yes', 'No'])->nullable()->comment('Politically Exposed Person');
            $table->string('name_of_pep', 100)->nullable();
            $table->string('relation_with_pep', 100)->nullable();
            $table->enum('any_action_by_sebi', ['Yes', 'No'])->nullable();
            $table->text('details_of_action')->nullable();
            $table->enum('dealing_with_other_stockbroker', ['Yes', 'No'])->nullable();
            $table->enum('any_dispute_with_stockbroker', ['Yes', 'No'])->nullable();
            $table->text('dispute_with_stockbroker_details')->nullable();
            $table->string('commodity_trade_classification', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('added_on')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regulatory_info');
    }
};
