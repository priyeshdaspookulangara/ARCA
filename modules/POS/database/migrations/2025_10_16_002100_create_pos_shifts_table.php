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
        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('terminal_id');
            $table->unsignedBigInteger('cashier_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->decimal('starting_cash', 15, 2);
            $table->decimal('ending_cash', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('terminal_id')->references('id')->on('pos_terminals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_shifts');
    }
};