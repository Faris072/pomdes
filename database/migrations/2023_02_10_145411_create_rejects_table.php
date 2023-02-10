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
        Schema::create('reject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transaction')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('status')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('is_active')->default(1);
            $table->text('description');
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
        Schema::dropIfExists('rejects');
    }
};
