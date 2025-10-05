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
        Schema::create('fina_co_pc_product_cost_headers', function (Blueprint $table) {
            $table->id();
            $table->string('product_id'); // Assuming product ID from another module
            $table->string('costing_variant');
            $table->date('costing_date');
            $table->decimal('total_cost', 15, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fina_co_pc_product_cost_headers');
    }
};