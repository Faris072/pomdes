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
        Schema::create('fuel_transaction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fuel_id')->constrained('fuel')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('transaction_id')->constrained('transaction')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name');
            $table->bigInteger('volume');
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
        Schema::dropIfExists('fuel_transaction');
    }
};
