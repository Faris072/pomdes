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
        Schema::create('invoice_pomdes_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_pomdes_id')->constrained('invoice_pomdes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name');
            $table->integer('size');
            $table->string('extension');
            $table->boolean('is_image');
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
        Schema::dropIfExists('invoice_pomdes_files');
    }
};
