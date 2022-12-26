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
        Schema::create('discrepancy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id');
            $table->string('fuel_type');
            $table->string('discrepancy_type');
            $table->float('fuel_volume');
            $table->bigInteger('fuel_price');
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
        Schema::dropIfExists('discrepancy');
    }
};
