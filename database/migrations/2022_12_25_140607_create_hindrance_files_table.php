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
        Schema::create('hindrance_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hindrance_id')->constrained('hindrance')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('hindrance_files');
    }
};
