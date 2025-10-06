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
        Schema::create('fina_ar_dunning_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_financials_id');
            $table->date('dunning_date');
            $table->integer('dunning_level');
            $table->text('dunning_notice_content');
            $table->timestamps();

            $table->foreign('customer_financials_id')->references('id')->on('fina_ar_customer_financials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fina_ar_dunning_history');
    }
};