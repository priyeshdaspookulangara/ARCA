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
        Schema::create('mm_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id');
            $table->string('movement_type'); // GR, GI, TRANSFER, ADJUSTMENT
            $table->decimal('quantity', 15, 2);
            $table->string('location_id');
            $table->unsignedBigInteger('reference_id')->nullable(); // e.g., goods_receipt_id, goods_issue_id
            $table->string('reference_type')->nullable(); // App\Models\GoodsReceipt
            $table->decimal('cost', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('mm_materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mm_stock_movements');
    }
};