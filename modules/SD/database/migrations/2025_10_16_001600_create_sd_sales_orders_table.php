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
        Schema::create('sd_sales_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('order_type');
            $table->string('status');
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('sd_customers');
        });

        Schema::create('sd_sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_order_id');
            $table->unsignedBigInteger('material_id');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->timestamps();

            $table->foreign('sales_order_id')->references('id')->on('sd_sales_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sd_sales_order_items');
        Schema::dropIfExists('sd_sales_orders');
    }
};