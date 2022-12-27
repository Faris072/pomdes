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
            $table->foreignId('discrepancy_id');
            $table->foreignId('fuel_transaction_id');
            $table->foreignId('discrepancy_type_id');
            $table->float('discrepancy_volume');
            $table->bigInteger('discrepancy_price');
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
