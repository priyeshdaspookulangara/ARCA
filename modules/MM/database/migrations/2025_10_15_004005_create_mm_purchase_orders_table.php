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
        Schema::create('mm_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->unsignedBigInteger('supplier_id');
            $table->date('order_date');
            $table->date('expected_delivery_date');
            $table->string('status'); // PENDING, APPROVED, PARTIALLY_RECEIVED, RECEIVED, CANCELLED
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('mm_suppliers');
        });

        Schema::create('mm_purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('mm_purchase_orders')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('mm_materials');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mm_purchase_order_items');
        Schema::dropIfExists('mm_purchase_orders');
    }
};