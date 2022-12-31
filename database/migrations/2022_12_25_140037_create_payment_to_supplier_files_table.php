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
        Schema::create('payment_to_supplier_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_to_supplier_id')->constrained('payment_to_supplier')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('payment_to_supplier_files');
    }
};
