<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuel_discrepancy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discrepancy_id')->constrained('discrepancy')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('fuel_transaction_id')->constrained('fuel_transaction')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('discrepancy_type_id')->constrained('discrepancy_type')->cascadeOnUpdate()->cascadeOnDelete();
            $table->double('discrepancy_volume');
            $table->double('discrepancy_price');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fuel_discrepancy');
    }
};
