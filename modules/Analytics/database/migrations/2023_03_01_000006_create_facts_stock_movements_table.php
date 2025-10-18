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
        Schema::create('facts_stock_movements', function (Blueprint $table) {
            $table->id('movement_id');
            $table->foreignId('date_id')->constrained('dim_date', 'date_id');
            $table->foreignId('store_id')->constrained('dim_stores', 'id');
            $table->foreignId('product_id')->constrained('dim_products', 'id');
            $table->integer('qty');
            $table->string('movement_type');
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
        Schema::dropIfExists('facts_stock_movements');
    }
};