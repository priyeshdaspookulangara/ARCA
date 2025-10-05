<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fina_co_pc_product_cost_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_cost_header_id');
            $table->unsignedBigInteger('cost_element_id')->nullable();
            $table->unsignedBigInteger('activity_type_id')->nullable();
            $table->decimal('quantity', 15, 5);
            $table->decimal('rate', 15, 5);
            $table->decimal('cost', 15, 2);
            $table->timestamps();

            $table->foreign('product_cost_header_id')->references('id')->on('fina_co_pc_product_cost_headers')->onDelete('cascade');
            $table->foreign('cost_element_id')->references('id')->on('fina_co_pc_cost_elements')->onDelete('cascade');
            $table->foreign('activity_type_id')->references('id')->on('fina_co_pc_activity_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fina_co_pc_product_cost_items');
    }
};