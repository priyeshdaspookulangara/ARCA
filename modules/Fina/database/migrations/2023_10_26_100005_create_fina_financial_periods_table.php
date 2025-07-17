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
        Schema::create('fina_financial_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fiscal_year_variant_id');
            $table->integer('year');
            $table->integer('period');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_open_for_posting');
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
        Schema::dropIfExists('fina_financial_periods');
    }
};
