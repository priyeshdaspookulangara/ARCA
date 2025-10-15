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
        Schema::create('mm_goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->date('receipt_date');
            $table->string('status'); // POSTED, CANCELLED
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('mm_purchase_orders');
        });

        Schema::create('mm_goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_receipt_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('quantity_received', 15, 2);
            $table->timestamps();

            $table->foreign('goods_receipt_id')->references('id')->on('mm_goods_receipts')->onDelete('cascade');
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
        Schema::dropIfExists('mm_goods_receipt_items');
        Schema::dropIfExists('mm_goods_receipts');
    }
};