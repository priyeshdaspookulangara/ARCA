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
        Schema::create('mm_materials', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('uom'); // Unit of Measure
            $table->string('valuation_method')->default('weighted_average'); // fifo, weighted_average
            $table->decimal('reorder_level', 15, 2)->default(0);
            $table->decimal('min_quantity', 15, 2)->default(0);
            $table->decimal('max_quantity', 15, 2)->default(0);
            $table->unsignedBigInteger('default_supplier_id')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->decimal('selling_price', 15, 2)->nullable();
            // FINA Integration
            $table->string('inventory_account')->nullable();
            $table->string('cogs_account')->nullable();
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
        Schema::dropIfExists('mm_materials');
    }
};