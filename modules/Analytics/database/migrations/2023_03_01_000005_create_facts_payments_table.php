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
        Schema::create('facts_payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('sale_id')->constrained('facts_sales', 'sale_id');
            $table->decimal('amount', 15, 2);
            $table->string('mode');
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
        Schema::dropIfExists('facts_payments');
    }
};