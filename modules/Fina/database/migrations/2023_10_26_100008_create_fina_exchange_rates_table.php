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
        Schema::create('fina_exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rate_type_id');
            $table->string('from_currency_code', 3);
            $table->string('to_currency_code', 3);
            $table->date('valid_from_date');
            $table->decimal('exchange_rate', 15, 6);
            $table->integer('ratio_from');
            $table->integer('ratio_to');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fina_exchange_rates');
    }
};
