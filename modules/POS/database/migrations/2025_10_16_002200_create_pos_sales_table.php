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
        Schema::create('pos_sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('tax_amount', 15, 2);
            $table->decimal('discount_amount', 15, 2);
            $table->timestamps();

            $table->foreign('shift_id')->references('id')->on('pos_shifts');
        });

        Schema::create('pos_sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('material_id');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('tax', 15, 2);
            $table->decimal('discount', 15, 2);
            $table->timestamps();

            $table->foreign('sale_id')->references('id')->on('pos_sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_sale_items');
        Schema::dropIfExists('pos_sales');
    }
};